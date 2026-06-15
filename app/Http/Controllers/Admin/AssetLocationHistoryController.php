<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetLocationHistory;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetLocationHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetLocationHistory::with(['asset', 'oldLocation', 'newLocation', 'changer'])
            ->latest();

        // Filter berdasarkan aset
        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('old_location_name', 'like', "%{$search}%")
                  ->orWhere('new_location_name', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        $histories = $query->paginate(50);
        
        // Data untuk filter
        $assets = Asset::orderBy('name')->get(['id', 'name', 'asset_code']);
        
        return view('admin.asset-location-history.index', compact('histories', 'assets'));
    }

    public function show(Asset $asset)
    {
        $histories = $asset->locationHistory()->paginate(50);
        return view('admin.asset-location-history.show', compact('asset', 'histories'));
    }

    public function timeline(Asset $asset)
    {
        $histories = $asset->locationHistory()->get();
        return view('admin.asset-location-history.timeline', compact('asset', 'histories'));
    }

    public function moveAsset(Request $request, Asset $asset)
    {
        $request->validate([
            'new_location_id' => 'required|exists:locations,id',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldLocationId = $asset->location_id;
            
            // Update asset location
            $asset->update([
                'location_id' => $request->new_location_id,
            ]);

            // Record history
            $asset->recordLocationChange(
                $oldLocationId, 
                $request->new_location_id,
                $request->reason,
                $request->notes
            );

            DB::commit();

            return redirect()->route('admin.assets.show', $asset)
                ->with('success', 'Lokasi aset berhasil dipindahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memindahkan aset: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        // Implement export Excel
        return back()->with('info', 'Fitur export sedang dalam pengembangan');
    }
}