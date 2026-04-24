<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik Aset
        $totalAssets = Asset::count();
        $assetsInUse = Asset::where('status', 'in_use')->count();
        $assetsAvailable = Asset::where('status', 'available')->count();
        $assetsMaintenance = Asset::where('status', 'maintenance')->count();
        $assetsDamaged = Asset::where('status', 'damaged')->count();
        
        // Total Nilai Aset
        $totalValue = Asset::sum('current_value');
        $totalPurchaseValue = Asset::sum('purchase_price');
        $totalDepreciation = $totalPurchaseValue - $totalValue;
        
        // Maintenance
        $pendingMaintenances = Maintenance::where('status', 'pending')->count();
        $inProgressMaintenances = Maintenance::where('status', 'in_progress')->count();
        $completedMaintenances = Maintenance::where('status', 'completed')->count();
        
        // Aset Terbaru (5 terakhir)
        $recentAssets = Asset::with(['category', 'location'])
                             ->latest()
                             ->limit(5)
                             ->get();
        
        // PERBAIKAN: Maintenance Mendatang (5 terdekat) - ganti scheduled_date dengan maintenance_date
        $upcomingMaintenances = Maintenance::with('asset')
                                           ->where('status', 'pending')
                                           ->where('maintenance_date', '>=', now())
                                           ->orderBy('maintenance_date', 'asc')
                                           ->limit(5)
                                           ->get();
        
        // Data untuk Grafik Aset per Kategori
        $categoryStats = Category::withCount('assets')
                                 ->having('assets_count', '>', 0)
                                 ->get()
                                 ->map(function ($category) {
                                     return [
                                         'name' => $category->name,
                                         'total' => $category->assets_count
                                     ];
                                 });
        
        // Data untuk Grafik Aset per Status
        $statusStats = [
            ['name' => 'Tersedia', 'total' => $assetsAvailable, 'color' => '#28a745'],
            ['name' => 'Digunakan', 'total' => $assetsInUse, 'color' => '#007bff'],
            ['name' => 'Maintenance', 'total' => $assetsMaintenance, 'color' => '#ffc107'],
            ['name' => 'Rusak', 'total' => $assetsDamaged, 'color' => '#dc3545'],
        ];
        
        // Data untuk Grafik Aset per Lokasi (Top 5)
        $locationStats = Location::withCount('assets')
                                 ->having('assets_count', '>', 0)
                                 ->orderBy('assets_count', 'desc')
                                 ->limit(5)
                                 ->get()
                                 ->map(function ($location) {
                                     return [
                                         'name' => $location->name,
                                         'total' => $location->assets_count
                                     ];
                                 });
        
        // Data untuk Grafik Maintenance per Bulan (6 bulan terakhir) - ganti scheduled_date dengan maintenance_date
        $maintenanceStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->format('M Y');
            $count = Maintenance::whereYear('maintenance_date', $month->year)
                                ->whereMonth('maintenance_date', $month->month)
                                ->count();
            $maintenanceStats[] = [
                'month' => $monthName,
                'total' => $count
            ];
        }
        
        return view('dashboard', compact(
            'totalAssets',
            'assetsInUse',
            'assetsAvailable',
            'assetsMaintenance',
            'assetsDamaged',
            'totalValue',
            'totalPurchaseValue',
            'totalDepreciation',
            'pendingMaintenances',
            'inProgressMaintenances',
            'completedMaintenances',
            'recentAssets',
            'upcomingMaintenances',
            'categoryStats',
            'statusStats',
            'locationStats',
            'maintenanceStats'
        ));
    }
}