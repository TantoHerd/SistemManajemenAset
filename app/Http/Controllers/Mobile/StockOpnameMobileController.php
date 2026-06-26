<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\StockOpnameSession;
use App\Models\StockOpnameItem;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StockOpnameMobileController extends Controller
{
    public function index()
    {
        $activeSession = StockOpnameSession::whereIn('status', ['draft', 'in_progress'])
            ->where('created_by', auth()->id())
            ->latest()
            ->first();

        $sessions = StockOpnameSession::where('created_by', auth()->id())
            ->latest()
            ->get();

        return view('mobile.stock-opname.index', compact('activeSession', 'sessions'));
    }

    public function scan($session)
    {
        $session = StockOpnameSession::findOrFail($session);
        
        if ($session->status == 'draft') {
            $session->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        $total = $session->items()->count();
        $scanned = $session->items()->whereNotNull('scanned_at')->count();
        $stats = [
            'total' => $total,
            'scanned' => $scanned,
            'remaining' => $total - $scanned,
            'progress' => $total > 0 ? round(($scanned / $total) * 100) : 0,
        ];

        return view('mobile.stock-opname.scan', compact('session', 'stats'));
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
            DB::beginTransaction();
            
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

            $remainingItems = $session->items()->whereNull('scanned_at')->count();
            $completed = $remainingItems == 0;

            if ($completed) {
                $session->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'completed' => $completed,
                'remaining' => $remainingItems
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sync(Request $request)
    {
        $data = $request->get('scans', []);
        
        foreach ($data as $scan) {
            // Process sync data
            // Untuk offline sync
        }

        return response()->json(['success' => true]);
    }

    private function getStats($session)
    {
        $total = $session->items()->count();
        $scanned = $session->items()->whereNotNull('scanned_at')->count();
        
        return [
            'total' => $total,
            'scanned' => $scanned,
            'remaining' => $total - $scanned,
            'progress' => $total > 0 ? round(($scanned / $total) * 100) : 0,
        ];
    }
}