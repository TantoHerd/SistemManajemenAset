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
        // Ambil sesi yang sedang berjalan atau draft
        $activeSession = StockOpnameSession::whereIn('status', ['draft', 'in_progress'])
            ->where('created_by', auth()->id())
            ->latest()
            ->first();

        $sessions = StockOpnameSession::where('created_by', auth()->id())
            ->latest()
            ->get();

        return view('mobile.stock-opname.index', compact('activeSession', 'sessions'));
    }

    public function scan(StockOpnameSession $session)
    {
        if ($session->status == 'draft') {
            $session->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        // Ambil item yang belum di-scan
        $nextItem = $session->items()
            ->with('asset')
            ->whereNull('scanned_at')
            ->first();

        $stats = $this->getStats($session);

        return view('mobile.stock-opname.scan', compact('session', 'nextItem', 'stats'));
    }

    public function scanAsset(Request $request, StockOpnameSession $session)
    {
        $barcode = $request->get('barcode');
        
        $item = $session->items()
            ->with('asset.location')
            ->whereHas('asset', function($q) use ($barcode) {
                $q->where('asset_code', $barcode)
                  ->orWhere('serial_number', $barcode);
            })
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Aset tidak ditemukan dalam sesi ini'
            ]);
        }

        if ($item->scanned_at) {
            return response()->json([
                'success' => false,
                'message' => 'Aset sudah di-scan'
            ]);
        }

        return response()->json([
            'success' => true,
            'itemId' => $item->id,
            'asset' => [
                'asset_code' => $item->asset->asset_code,
                'name' => $item->asset->name,
                'location_name' => $item->asset->location->name ?? '-',
                'condition' => $item->asset->condition ?? 'Baik',
            ]
        ]);
    }

    public function submitScan(Request $request, StockOpnameSession $session, StockOpnameItem $item)
    {
        $request->validate([
            'actual_status' => 'required|in:found,missing,damaged,moved',
            'actual_location' => 'required_if:actual_status,moved|nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $item->update([
                'actual_status' => $request->actual_status,
                'actual_location' => $request->actual_location,
                'notes' => $request->notes,
                'scanned_by' => auth()->id(),
                'scanned_at' => now(),
            ]);

            if ($request->actual_status == 'moved' && $request->actual_location) {
                $location = \App\Models\Location::where('name', 'LIKE', '%' . $request->actual_location . '%')->first();
                if ($location) {
                    $item->asset->update(['location_id' => $location->id]);
                }
            }

            if ($request->actual_status == 'damaged') {
                $item->asset->update(['status' => 'damaged']);
            }

            DB::commit();

            $remainingItems = $session->items()->whereNull('scanned_at')->count();
            $completed = $remainingItems == 0;

            if ($completed) {
                $session->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }

            // Cache progress untuk offline sync
            Cache::put("mobile_scan_{$session->id}_{$item->id}", [
                'scanned_at' => now(),
                'status' => $request->actual_status
            ], 3600);

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