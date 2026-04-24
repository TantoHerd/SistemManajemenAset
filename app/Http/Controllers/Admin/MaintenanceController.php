<?php

namespace App\Http\Controllers\Admin;

use App\Models\Asset;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::with('asset')->latest()->paginate(10);
        return view('admin.maintenances.index', compact('maintenances'));
    }

    public function create()
    {
        $assets = Asset::orderBy('name')->get();
        return view('admin.maintenances.create', compact('assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'maintenance_date' => 'required|date',
            'technician' => 'nullable|string|max:100',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        Maintenance::create($request->all());

        // Update status aset menjadi maintenance
        $asset = Asset::find($request->asset_id);
        if ($asset && $asset->status !== 'maintenance') {
            $asset->status = 'maintenance';
            $asset->save();
        }

        return redirect()->back()->with('success', 'Maintenance berhasil dikirim');
    }

    public function show(Maintenance $maintenance)
    {
        return view('admin.maintenances.show', compact('maintenance'));
    }

    public function edit(Maintenance $maintenance)
    {
        $assets = Asset::orderBy('name')->get();
        return view('admin.maintenances.edit', compact('maintenance', 'assets'));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'maintenance_date' => 'required|date',
            'technician' => 'nullable|string|max:100',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $maintenance->update($request->all());

        return redirect()->route('admin.maintenances.index')
            ->with('success', 'Maintenance berhasil diperbarui');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        return redirect()->route('admin.maintenances.index')
            ->with('success', 'Maintenance berhasil dihapus');
    }

    /**
     * Jadwal Maintenance (Upcoming)
     */
    public function schedule()
    {
        $maintenances = Maintenance::with('asset')
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('maintenance_date', '>=', Carbon::today())
            ->orderBy('maintenance_date', 'asc')
            ->paginate(10);
        
        return view('admin.maintenances.schedule', compact('maintenances'));
    }

    /**
     * Riwayat Maintenance (Completed)
     */
    public function history()
    {
        $maintenances = Maintenance::with('asset')
            ->where('status', 'completed')
            ->orderBy('maintenance_date', 'desc')
            ->paginate(10);
        
        return view('admin.maintenances.history', compact('maintenances'));
    }

    /**
     * Laporan Maintenance
     */
    public function report(Request $request)
    {
        $query = Maintenance::with('asset');
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('maintenance_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('maintenance_date', '<=', $request->date_to);
        }
        
        // Filter by asset
        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $maintenances = $query->orderBy('maintenance_date', 'desc')->paginate(10);
        $assets = Asset::orderBy('name')->get();
        $statuses = Maintenance::$statuses;
        
        // Stats
        $stats = [
            'total' => $query->count(),
            'total_cost' => $query->sum('cost'),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
        ];
        
        return view('admin.maintenances.report', compact('maintenances', 'assets', 'statuses', 'stats'));
    }
}