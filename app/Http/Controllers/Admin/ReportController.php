<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Maintenance;
use App\Models\Loan;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        // Filter
        $categoryId = $request->category_id;
        $locationId = $request->location_id;
        $dateFrom = $request->date_from ?? now()->subYear()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');

        // ============================================
        // 1. ASET BY KATEGORI
        // ============================================
        $assetsByCategory = Category::withCount(['assets' => function($q) use ($categoryId, $locationId) {
            if ($categoryId) {
                $q->where('category_id', $categoryId);
            }
            if ($locationId) {
                $locationIds = $this->getLocationIds($locationId);
                $q->whereIn('location_id', $locationIds);
            }
        }])->having('assets_count', '>', 0)->orderByDesc('assets_count')->get();

        // ============================================
        // 2. ASET BY STATUS
        // ============================================
        $assetsByStatus = Asset::select('status', DB::raw('count(*) as total'))
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($locationId, function($q) use ($locationId) {
                $locationIds = $this->getLocationIds($locationId);
                $q->whereIn('location_id', $locationIds);
            })
            ->groupBy('status')->get();

        // ============================================
        // 3. ASET BY LOKASI
        // ============================================
        $assetsByLocation = Location::withCount(['assets' => function($q) use ($categoryId) {
            if ($categoryId) $q->where('category_id', $categoryId);
        }])->having('assets_count', '>', 0)->orderByDesc('assets_count')->get();

        // ============================================
        // 4. NILAI ASET
        // ============================================
        $totalValue = Asset::when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($locationId, fn($q) => $q->where('location_id', $locationId))
            ->sum('current_value');
        $totalPurchaseValue = Asset::when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($locationId, fn($q) => $q->where('location_id', $locationId))
            ->sum('purchase_price');
        $totalDepreciation = $totalPurchaseValue - $totalValue;

        // ============================================
        // 5. MAINTENANCE
        // ============================================
        $maintenances = Maintenance::with('asset')
            ->when($categoryId, fn($q) => $q->whereHas('asset', fn($q) => $q->where('category_id', $categoryId)))
            ->when($locationId, fn($q) => $q->whereHas('asset', fn($q) => $q->where('location_id', $locationId)))
            ->whereBetween('maintenance_date', [$dateFrom, $dateTo])
            ->latest()->limit(10)->get();

        $totalMaintenanceCost = Maintenance::whereBetween('maintenance_date', [$dateFrom, $dateTo])
            ->when($categoryId, fn($q) => $q->whereHas('asset', fn($q) => $q->where('category_id', $categoryId)))
            ->sum('cost');

        // ============================================
        // 6. MAINTENANCE PER BULAN (Chart)
        // ============================================
        $maintenanceChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Maintenance::whereYear('maintenance_date', $month->year)
                ->whereMonth('maintenance_date', $month->month)
                ->when($categoryId, fn($q) => $q->whereHas('asset', fn($q) => $q->where('category_id', $categoryId)))
                ->count();
            $cost = Maintenance::whereYear('maintenance_date', $month->year)
                ->whereMonth('maintenance_date', $month->month)
                ->when($categoryId, fn($q) => $q->whereHas('asset', fn($q) => $q->where('category_id', $categoryId)))
                ->sum('cost');
            $maintenanceChart[] = [
                'month' => $month->translatedFormat('M Y'),
                'count' => $count,
                'cost' => $cost
            ];
        }

        // ============================================
        // 7. PEMINJAMAN
        // ============================================
        $loans = Loan::with(['asset', 'user'])
            ->when($categoryId, fn($q) => $q->whereHas('asset', fn($q) => $q->where('category_id', $categoryId)))
            ->whereBetween('loan_date', [$dateFrom, $dateTo])
            ->latest()->limit(10)->get();

        $totalLoans = Loan::whereBetween('loan_date', [$dateFrom, $dateTo])
            ->when($categoryId, fn($q) => $q->whereHas('asset', fn($q) => $q->where('category_id', $categoryId)))
            ->count();
        $overdueLoans = Loan::where('status', 'overdue')
            ->when($categoryId, fn($q) => $q->whereHas('asset', fn($q) => $q->where('category_id', $categoryId)))
            ->count();

        return view('admin.reports.index', compact(
            'categories', 'locations',
            'categoryId', 'locationId', 'dateFrom', 'dateTo',
            'assetsByCategory', 'assetsByStatus', 'assetsByLocation',
            'totalValue', 'totalPurchaseValue', 'totalDepreciation',
            'maintenances', 'totalMaintenanceCost', 'maintenanceChart',
            'loans', 'totalLoans', 'overdueLoans'
        ));
    }

    /**
     * Export laporan ke Excel.
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(new ReportExport($request->all()), 'laporan_aset_' . date('Y-m-d_His') . '.xlsx');
    }

    /**
     * Export laporan ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);
        
        $pdf = Pdf::loadView('admin.reports.pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('laporan_aset_' . date('Y-m-d_His') . '.pdf');
    }

    /**
     * Ambil data laporan (shared method).
     */
    private function getReportData(Request $request)
    {
        $categoryId = $request->category_id;
        $locationId = $request->location_id;
        $dateFrom = $request->date_from ?? now()->subYear()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');

        // Helper untuk filter lokasi (termasuk sub-lokasi)
        $locationFilter = function($q) use ($locationId) {
            if ($locationId) {
                $locationIds = $this->getLocationIds($locationId);
                $q->whereIn('location_id', $locationIds);
            }
        };

        $totalValue = Asset::when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->where($locationFilter)->sum('current_value');

        $totalPurchaseValue = Asset::when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->where($locationFilter)->sum('purchase_price');

        $totalMaintenanceCost = Maintenance::whereBetween('maintenance_date', [$dateFrom, $dateTo])
            ->when($categoryId, fn($q) => $q->whereHas('asset', fn($q) => $q->where('category_id', $categoryId)))
            ->when($locationId, fn($q) => $q->whereHas('asset', $locationFilter))
            ->sum('cost');

        $assets = Asset::with(['category', 'location'])
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->where($locationFilter)
            ->latest()->get();

        $maintenances = Maintenance::with('asset')
            ->when($categoryId, fn($q) => $q->whereHas('asset', fn($q) => $q->where('category_id', $categoryId)))
            ->when($locationId, fn($q) => $q->whereHas('asset', $locationFilter))
            ->whereBetween('maintenance_date', [$dateFrom, $dateTo])
            ->latest()->get();

        $loans = Loan::with(['asset', 'user'])
            ->when($categoryId, fn($q) => $q->whereHas('asset', fn($q) => $q->where('category_id', $categoryId)))
            ->whereBetween('loan_date', [$dateFrom, $dateTo])
            ->latest()->get();

        $companyName = Setting::where('key', 'company_name')->value('value') ?? 'PT. NAMA PERUSAHAAN';
        $systemName = Setting::where('key', 'system_name')->value('value') ?? 'SIMASET';

        return compact(
            'totalValue', 'totalPurchaseValue', 'totalMaintenanceCost',
            'assets', 'maintenances', 'loans',
            'dateFrom', 'dateTo', 'companyName', 'systemName',
            'categoryId', 'locationId'
        );
    }

    /**
         * Mendapatkan semua ID lokasi dari lokasi induk (termasuk sub-lokasi)
         */
        public function getLocationIds($parentId)
        {
            $ids = [$parentId];
            $children = \App\Models\Location::where('parent_id', $parentId)->get();
            
            foreach ($children as $child) {
                $ids = array_merge($ids, $this->getLocationIds($child->id));
            }
            
            return $ids;
        }
}