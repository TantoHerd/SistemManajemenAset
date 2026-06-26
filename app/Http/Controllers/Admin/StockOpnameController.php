<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockOpnameSession;
use App\Models\StockOpnameItem;
use App\Models\Asset;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        $sessions = StockOpnameSession::with(['location', 'creator'])
            ->latest()
            ->paginate(10);
        return view('admin.stock-opname.index', compact('sessions'));
    }

    public function create()
    {
        $locations = Location::orderBy('name')->get();
        return view('admin.stock-opname.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Buat session
            $session = StockOpnameSession::create([
                'name' => $request->name,
                'location_id' => $request->location_id,
                'notes' => $request->notes,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Ambil aset berdasarkan lokasi
            $assetsQuery = Asset::query();
            
            if ($request->location_id) {
                $assetsQuery->where('location_id', $request->location_id);
            }
            
            $assets = $assetsQuery->get();

            // Buat item stock opname
            foreach ($assets as $asset) {
                StockOpnameItem::create([
                    'session_id' => $session->id,
                    'asset_id' => $asset->id,
                    'expected_location' => $asset->location?->name,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.stock-opname.show', $session)
                ->with('success', 'Sesi Stock Opname berhasil dibuat. Total ' . $assets->count() . ' aset.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat sesi: ' . $e->getMessage());
        }
    }

    public function show(StockOpnameSession $stockOpname)
    {
        // Load items dengan asset
        $stockOpname->load(['items.asset', 'location', 'creator']);
        
        // Hitung progress
        $totalItems = $stockOpname->items->count();
        $scannedItems = $stockOpname->items->whereNotNull('scanned_at')->count();
        $progress = $totalItems > 0 ? round(($scannedItems / $totalItems) * 100) : 0;
        
        // Statistik
        $summary = $stockOpname->summary;
        
        return view('admin.stock-opname.show', compact('stockOpname', 'progress', 'summary'));
    }

    public function start(StockOpnameSession $stockOpname)
    {
        if ($stockOpname->status != 'draft') {
            return back()->with('error', 'Sesi sudah dimulai atau selesai.');
        }

        $stockOpname->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return redirect()->route('admin.stock-opname.scan', $stockOpname)
            ->with('success', 'Sesi Stock Opname dimulai. Silakan scan aset.');
    }

    public function scan(StockOpnameSession $stockOpname)
    {
        if ($stockOpname->status != 'in_progress') {
            return redirect()->route('admin.stock-opname.show', $stockOpname)
                ->with('error', 'Sesi belum dimulai atau sudah selesai.');
        }

        // Ambil item yang belum di-scan
        $nextItem = $stockOpname->items()
            ->with('asset')
            ->whereNull('scanned_at')
            ->first();

        if (!$nextItem) {
            return redirect()->route('admin.stock-opname.complete', $stockOpname)
                ->with('success', 'Semua aset telah di-scan!');
        }

        // Kirim variabel ke view
        $total = $stockOpname->items()->count();
        $scanned = $stockOpname->items()->whereNotNull('scanned_at')->count();
        
        return view('admin.stock-opname.scan', compact('stockOpname', 'nextItem', 'total', 'scanned'));
    }

    public function processScan(Request $request, $sessionId, $itemId)
    {
        $session = StockOpnameSession::find($sessionId);
        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak ditemukan'
            ]);
        }
        
        $item = StockOpnameItem::find($itemId);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ]);
        }
        
        $request->validate([
            'actual_status' => 'required|in:found,missing,damaged,moved',
            'actual_location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $item->update([
                'actual_status' => $request->actual_status,
                'actual_location' => $request->actual_location,
                'notes' => $request->notes,
                'scanned_by' => auth()->id(),
                'scanned_at' => now(),
            ]);

            // Jika aset berpindah lokasi
            if ($request->actual_status == 'moved' && $request->actual_location) {
                $location = \App\Models\Location::where('name', 'LIKE', '%' . $request->actual_location . '%')->first();
                if ($location) {
                    $item->asset->update(['location_id' => $location->id]);
                }
            }

            // Jika aset rusak
            if ($request->actual_status == 'damaged') {
                $item->asset->update(['status' => 'damaged']);
            }

            // Cek apakah semua sudah di-scan
            $remainingItems = $session->items()->whereNull('scanned_at')->count();
            $completed = $remainingItems == 0;

            if ($completed) {
                $session->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'completed' => $completed,
                'remaining' => $remainingItems
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function complete(StockOpnameSession $stockOpname)
    {
        if ($stockOpname->status != 'in_progress') {
            return redirect()->route('admin.stock-opname.show', $stockOpname);
        }

        $stockOpname->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('admin.stock-opname.report', $stockOpname)
            ->with('success', 'Stock Opname selesai! Lihat laporan di bawah.');
    }

    public function report(StockOpnameSession $stockOpname)
    {
        $stockOpname->load(['items.asset', 'location', 'creator']);
        
        // Hitung ulang progress
        $total = $stockOpname->items->count();
        $scanned = $stockOpname->items->whereNotNull('scanned_at')->count();
        $progress = $total > 0 ? round(($scanned / $total) * 100) : 0;
        
        $summary = $stockOpname->summary;
        
        return view('admin.stock-opname.report', compact('stockOpname', 'summary', 'total', 'scanned', 'progress'));
    }

    public function export(StockOpnameSession $stockOpname)
    {
        // Implement export Excel
        // Bisa pakai Maatwebsite/Excel
    }

    public function destroy(StockOpnameSession $stockOpname)
    {
        $stockOpname->delete();
        return redirect()->route('admin.stock-opname.index')
            ->with('success', 'Sesi Stock Opname dihapus.');
    }

    public function scanAsset(Request $request, $session)
{
    $session = StockOpnameSession::find($session);
    if (!$session) {
        return response()->json([
            'success' => false,
            'message' => 'Sesi tidak ditemukan'
        ]);
    }
    
    $barcode = $request->get('barcode');
    
    if (!$barcode) {
        return response()->json([
            'success' => false,
            'message' => 'Barcode tidak boleh kosong'
        ]);
    }
    
    $item = $session->items()
        ->with('asset.location')
        ->whereHas('asset', function($q) use ($barcode) {
            $q->where('asset_code', $barcode)
              ->orWhere('serial_number', $barcode)
              ->orWhere('id', $barcode);
        })
        ->first();
    
    if (!$item) {
        return response()->json([
            'success' => false,
            'message' => 'Aset dengan kode ' . $barcode . ' tidak ditemukan dalam sesi ini'
        ]);
    }
    
    if ($item->scanned_at) {
        return response()->json([
            'success' => false,
            'message' => 'Aset ' . $item->asset->name . ' sudah di-scan sebelumnya'
        ]);
    }
    
    return response()->json([
        'success' => true,
        'itemId' => $item->id,
        'asset' => [
            'asset_code' => $item->asset->asset_code,
            'name' => $item->asset->name,
            'location_name' => $item->asset->location->name ?? $item->expected_location ?? '-',
            'condition' => $item->asset->condition ?? 'Baik',
        ]
    ]);
}

    public function submitScan(Request $request, $session, $item)
    {
        $session = StockOpnameSession::find($session);
        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak ditemukan'
            ]);
        }
        
        $item = StockOpnameItem::find($item);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ]);
        }

        $request->validate([
            'actual_status' => 'required|in:found,missing,damaged,moved',
            'actual_location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $item->update([
                'actual_status' => $request->actual_status,
                'actual_location' => $request->actual_location,
                'notes' => $request->notes,
                'scanned_by' => auth()->id(),
                'scanned_at' => now(),
            ]);

            $remainingItems = $session->items()->whereNull('scanned_at')->count();
            $completed = $remainingItems == 0;

            if ($completed) {
                $session->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'completed' => $completed,
                'remaining' => $remainingItems
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}