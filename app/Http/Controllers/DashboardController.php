<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ============================================
        // STATISTIK UTAMA
        // ============================================
        
        // Total Aset
        $totalAssets = Asset::count();
        $assetsInUse = Asset::where('status', 'in_use')->count();
        $assetsAvailable = Asset::where('status', 'available')->count();
        $assetsMaintenance = Asset::where('status', 'maintenance')->count();
        $assetsDamaged = Asset::where('status', 'damaged')->count();
        $assetsDisposed = Asset::where('status', 'disposed')->count();
        
        // Total Nilai Aset
        $totalValue = Asset::sum('current_value');
        $totalPurchaseValue = Asset::sum('purchase_price');
        $totalDepreciation = $totalPurchaseValue - $totalValue;
        
        // Persentase penggunaan
        $usagePercentage = $totalAssets > 0 ? round(($assetsInUse / $totalAssets) * 100) : 0;
        $availablePercentage = $totalAssets > 0 ? round(($assetsAvailable / $totalAssets) * 100) : 0;
        
        // Maintenance Stats
        $pendingMaintenances = Maintenance::where('status', 'pending')->count();
        $inProgressMaintenances = Maintenance::where('status', 'in_progress')->count();
        $completedMaintenances = Maintenance::where('status', 'completed')->count();
        $totalMaintenanceCost = Maintenance::sum('cost');
        
        // Total Users & Locations
        $totalUsers = User::count();
        $totalLocations = Location::count();
        $totalCategories = Category::count();
        
        // ============================================
        // ASET TERBARU (5 terakhir)
        // ============================================
        $recentAssets = Asset::with(['category', 'location'])
                             ->latest()
                             ->limit(5)
                             ->get();
        
        // ============================================
        // MAINTENANCE MENDATANG (5 terdekat) - FIXED
        // ============================================
        $upcomingMaintenances = Maintenance::with('asset')
                                           ->whereIn('status', ['pending', 'in_progress'])
                                           ->whereNotNull('maintenance_date')
                                           ->where('maintenance_date', '>=', now())
                                           ->orderBy('maintenance_date', 'asc')
                                           ->limit(5)
                                           ->get()
                                           ->map(function ($maintenance) {
                                               $maintenance->days_until = now()->diffInDays($maintenance->maintenance_date);
                                               return $maintenance;
                                           });
        
        // ============================================
        // DATA UNTUK CHART: Aset per Kategori (Pie)
        // ============================================
        $categoryStats = Category::withCount('assets')
                                 ->having('assets_count', '>', 0)
                                 ->orderByDesc('assets_count')
                                 ->get()
                                 ->map(function ($category) {
                                     return [
                                         'name' => $category->name,
                                         'total' => $category->assets_count,
                                         'color' => null // akan diisi di JS
                                     ];
                                 });
        
        // ============================================
        // DATA UNTUK CHART: Aset per Status (Doughnut)
        // ============================================
        $statusStats = [
            ['name' => 'Tersedia', 'total' => $assetsAvailable, 'color' => '#28a745', 'icon' => 'check-circle'],
            ['name' => 'Digunakan', 'total' => $assetsInUse, 'color' => '#4361ee', 'icon' => 'person-check'],
            ['name' => 'Maintenance', 'total' => $assetsMaintenance, 'color' => '#ffc107', 'icon' => 'wrench'],
            ['name' => 'Rusak', 'total' => $assetsDamaged, 'color' => '#dc3545', 'icon' => 'exclamation-triangle'],
            ['name' => 'Dihapuskan', 'total' => $assetsDisposed, 'color' => '#6c757d', 'icon' => 'trash'],
        ];
        
        // ============================================
        // DATA UNTUK CHART: Aset per Lokasi (Horizontal Bar)
        // ============================================
        $locationStats = Location::withCount('assets')
                                 ->having('assets_count', '>', 0)
                                 ->orderByDesc('assets_count')
                                 ->limit(8)
                                 ->get()
                                 ->map(function ($location) {
                                     return [
                                         'name' => $location->name,
                                         'total' => $location->assets_count
                                     ];
                                 });
        
        // ============================================
        // DATA UNTUK CHART: Nilai Aset per Kategori
        // ============================================
        $valueByCategory = Category::withSum('assets', 'current_value')
                                   ->having('assets_sum_current_value', '>', 0)
                                   ->orderByDesc('assets_sum_current_value')
                                   ->limit(5)
                                   ->get()
                                   ->map(function ($category) {
                                       return [
                                           'name' => $category->name,
                                           'value' => $category->assets_sum_current_value
                                       ];
                                   });
        
        // ============================================
        // DATA UNTUK CHART: Maintenance per Bulan (Line)
        // ============================================
        $maintenanceStats = [];
        $maintenanceCostStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->translatedFormat('M Y'); // Format Indonesia
            $count = Maintenance::whereYear('maintenance_date', $month->year)
                                ->whereMonth('maintenance_date', $month->month)
                                ->count();
            $cost = Maintenance::whereYear('maintenance_date', $month->year)
                               ->whereMonth('maintenance_date', $month->month)
                               ->sum('cost');
            
            $maintenanceStats[] = [
                'month' => $monthName,
                'total' => $count
            ];
            $maintenanceCostStats[] = [
                'month' => $monthName,
                'cost' => $cost
            ];
        }
        
        // ============================================
        // ASET HAMPIR HABIS GARANSI
        // ============================================
        $expiringWarranty = Asset::whereNotNull('warranty_expiry')
                                 ->where('warranty_expiry', '>=', now())
                                 ->where('warranty_expiry', '<=', now()->addMonths(3))
                                 ->with('category')
                                 ->orderBy('warranty_expiry', 'asc')
                                 ->limit(5)
                                 ->get()
                                 ->map(function ($asset) {
                                     $asset->days_until_warranty = now()->diffInDays($asset->warranty_expiry);
                                     return $asset;
                                 });
        
        return view('dashboard', compact(
            // Statistik utama
            'totalAssets',
            'assetsInUse',
            'assetsAvailable',
            'assetsMaintenance',
            'assetsDamaged',
            'assetsDisposed',
            'totalValue',
            'totalPurchaseValue',
            'totalDepreciation',
            'usagePercentage',
            'availablePercentage',
            
            // Maintenance
            'pendingMaintenances',
            'inProgressMaintenances',
            'completedMaintenances',
            'totalMaintenanceCost',
            
            // Data tambahan
            'totalUsers',
            'totalLocations',
            'totalCategories',
            
            // List
            'recentAssets',
            'upcomingMaintenances',
            'expiringWarranty',
            
            // Charts
            'categoryStats',
            'statusStats',
            'locationStats',
            'valueByCategory',
            'maintenanceStats',
            'maintenanceCostStats'
        ));
    }
}