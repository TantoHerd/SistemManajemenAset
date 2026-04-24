<?php

namespace App\Http\Controllers\Admin;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * Display a listing of locations.
     */
    public function index()
    {
        $locations = Location::with('parent')
                              ->withCount('assets')
                              ->orderBy('name')
                              ->paginate(10);
        
        $rootLocations = Location::rootLocations()->get();
        
        return view('admin.locations.index', compact('locations', 'rootLocations'));
    }

    /**
     * Show the form for creating a new location.
     */
    public function create()
    {
        $parents = Location::orderBy('name')->get();
        
        return view('admin.locations.create', compact('parents'));
    }

    /**
     * Store a newly created location in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:locations,code',
            'parent_id' => 'nullable|exists:locations,id',
            'building' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        Location::create($request->all());

        return redirect()->route('admin.locations.index')
                         ->with('success', 'Lokasi berhasil ditambahkan');
    }

    /**
     * Display the specified location.
     */
    public function show(Location $location)
    {
        $location->load(['children', 'assets' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('admin.locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(Location $location)
    {
        $parents = Location::where('id', '!=', $location->id)
                           ->orderBy('name')
                           ->get();
        
        return view('admin.locations.edit', compact('location', 'parents'));
    }

    /**
     * Update the specified location in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:locations,code,' . $location->id,
            'parent_id' => 'nullable|exists:locations,id',
            'building' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ]);

        // Prevent circular reference
        if ($request->parent_id == $location->id) {
            return redirect()->back()
                             ->withErrors(['parent_id' => 'Lokasi tidak dapat menjadi parent dari dirinya sendiri'])
                             ->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        $location->update($request->all());

        return redirect()->route('admin.locations.index')
                         ->with('success', 'Lokasi berhasil diperbarui');
    }

    /**
     * Remove the specified location from storage.
     */
    public function destroy(Location $location)
    {
        // Check if location has child locations
        if ($location->children()->count() > 0) {
            return redirect()->back()
                             ->with('error', 'Lokasi tidak dapat dihapus karena masih memiliki sub-lokasi');
        }
        
        // Check if location has assets
        if ($location->assets()->count() > 0) {
            return redirect()->back()
                             ->with('error', 'Lokasi tidak dapat dihapus karena masih memiliki aset');
        }

        $location->delete();

        return redirect()->route('admin.locations.index')
                         ->with('success', 'Lokasi berhasil dihapus');
    }
}