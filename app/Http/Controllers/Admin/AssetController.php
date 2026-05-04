<?php

namespace App\Http\Controllers\Admin;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use App\Models\AssetSpecification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Exports\AssetsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AmortizationExport;
use App\Imports\AssetsImport;
use App\Exports\ImportTemplateExport;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    /**
     * Display a listing of assets.
     */
    public function index(Request $request)
    {
        // Simpan filter ke session (tanpa assigned_to)
        if ($request->has('category') || $request->has('location') || $request->has('status') || $request->has('search')) {
            session([
                'asset_filter_category' => $request->category,
                'asset_filter_location' => $request->location,
                'asset_filter_status' => $request->status,
                'asset_filter_search' => $request->search,
            ]);
        }
        
        // Ambil nilai dari session
        $categoryFilter = session('asset_filter_category', $request->category);
        $locationFilter = session('asset_filter_location', $request->location);
        $statusFilter = session('asset_filter_status', $request->status);
        $searchFilter = session('asset_filter_search', $request->search);
        
        // Jumlah baris per halaman
        $perPage = $request->get('per_page', session('asset_per_page', 15));
        session(['asset_per_page' => $perPage]);
        
        $query = Asset::with(['category', 'location', 'assignedTo']);
        
        // Apply filters
        if ($categoryFilter) {
            $query->where('category_id', $categoryFilter);
        }
        
        // Filter lokasi induk - mencakup semua sub lokasi
        if ($locationFilter) {
            $locationIds = $this->getLocationIds($locationFilter);
            $query->whereIn('location_id', $locationIds);
        }
        
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        
        if ($searchFilter) {
            $query->where(function($q) use ($searchFilter) {
                $q->where('asset_code', 'like', "%{$searchFilter}%")
                  ->orWhere('name', 'like', "%{$searchFilter}%")
                  ->orWhere('serial_number', 'like', "%{$searchFilter}%");
            });
        }
        
        $assets = $query->latest()->paginate($perPage);
        
        // Data untuk filter
        $locations = Location::whereNull('parent_id')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $statuses = Asset::$statuses;
        
        return view('admin.assets.index', compact('assets', 'categories', 'locations', 'statuses', 
                    'categoryFilter', 'locationFilter', 'statusFilter', 'searchFilter', 'perPage'));
    }
    
    /**
     * Mendapatkan semua ID lokasi dari lokasi induk (termasuk sub-lokasi)
     */
    private function getLocationIds($parentId)
    {
        $ids = [$parentId];
        $children = Location::where('parent_id', $parentId)->get();
        
        foreach ($children as $child) {
            $ids = array_merge($ids, $this->getLocationIds($child->id));
        }
        
        return $ids;
    }
    
    public function resetFilter()
    {
        // Hapus semua session filter
        session()->forget([
            'asset_filter_category', 
            'asset_filter_location', 
            'asset_filter_status', 
            'asset_filter_assigned_to', 
            'asset_filter_search',
            'asset_per_page'
        ]);
        
        // Juga hapus dari request
        return response()->json(['success' => true]);
    }

    /**
     * Show the form for creating a new asset.
     */
    public function create()
    {
        $categories = Category::with(['activeSpecifications' => function($query) {
            $query->orderBy('sort_order');
        }])->orderBy('name')->get();
        
        $locations = Location::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $statuses = Asset::$statuses;
        
        return view('admin.assets.create', compact('categories', 'locations', 'users', 'statuses'));
    }

    /**
     * Store a newly created asset in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_code' => 'nullable|string|max:50|unique:assets,asset_code',
            'name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number',
            'model' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:' . implode(',', array_keys(Asset::$statuses)),
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'residual_value' => 'nullable|numeric|min:0',
            'useful_life_months' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'warranty_expiry' => 'nullable|date',
            // Spesifikasi tidak wajib di validasi karena dinamis
            'specifications' => 'nullable|array',
            'specifications.*' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        // Get category for default useful life
        $category = Category::find($request->category_id);
        
        $data = $request->except(['specifications']);
        
        // Set default useful life from category if not provided
        if (empty($data['useful_life_months'])) {
            $data['useful_life_months'] = $category->useful_life_months;
        }
        
        // Set default residual value (10% of purchase price) if not provided
        if (empty($data['residual_value'])) {
            $data['residual_value'] = $data['purchase_price'] * 0.1;
        }
        
        // Set initial current value
        $data['current_value'] = $data['purchase_price'];
        
        // Auto generate asset code if not provided
        if (empty($data['asset_code'])) {
            $data['asset_code'] = $this->generateAssetCode();
        }
        
        $asset = Asset::create($data);
        
        // ========== SIMPAN SPESIFIKASI ==========
        if ($request->has('specifications')) {
            $this->saveSpecifications($asset, $request->specifications, $category);
        }
        
        // Generate barcode image
        $this->generateBarcodeImage($asset);
    
        return redirect()->route('admin.assets.index')
                        ->with('success', 'Aset berhasil ditambahkan');
    }

    /**
     * Display the specified asset.
     */
    public function show(Asset $asset)
    {
        $asset->load(['category', 'location', 'assignedTo', 'maintenances', 'specifications', 'documents']);
        
        return view('admin.assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified asset.
     */
    public function edit(Asset $asset)
    {
        $categories = Category::with(['activeSpecifications' => function($query) {
            $query->orderBy('sort_order');
        }])->orderBy('name')->get();
        
        $locations = Location::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $statuses = Asset::$statuses;
        
        // Load specifications untuk aset ini
        $asset->load('specifications');
        $assetSpecs = $asset->specifications->pluck('spec_value', 'spec_key')->toArray();
        
        return view('admin.assets.edit', compact('asset', 'categories', 'locations', 'users', 'statuses', 'assetSpecs'));
    }

    /**
     * Update the specified asset in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $validator = Validator::make($request->all(), [
            'asset_code' => 'required|string|max:50|unique:assets,asset_code,' . $asset->id,
            'name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number,' . $asset->id,
            'model' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:' . implode(',', array_keys(Asset::$statuses)),
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'residual_value' => 'nullable|numeric|min:0',
            'useful_life_months' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'warranty_expiry' => 'nullable|date',
            // Spesifikasi
            'specifications' => 'nullable|array',
            'specifications.*' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        $data = $request->except(['specifications']);
        
        // Recalculate current value if purchase price or useful life changed
        if ($asset->purchase_price != $data['purchase_price'] || 
            $asset->useful_life_months != $data['useful_life_months']) {
            $data['current_value'] = $data['purchase_price'];
        }
        
        $asset->update($data);
        
        // ========== UPDATE SPESIFIKASI ==========
        $category = Category::find($request->category_id);
        
        // Hapus spesifikasi lama yang tidak ada di request baru
        if ($request->has('specifications')) {
            $requestedKeys = array_keys(array_filter($request->specifications, function($value) {
                return $value !== null && $value !== '';
            }));
        } else {
            $requestedKeys = [];
        }
        
        // Hapus spesifikasi yang tidak dikirim
        $asset->specifications()
              ->whereNotIn('spec_key', $requestedKeys)
              ->delete();
        
        // Simpan/update spesifikasi baru
        if ($request->has('specifications')) {
            $this->saveSpecifications($asset, $request->specifications, $category);
        }
        
        // Regenerate barcode if asset code changed
        if ($asset->wasChanged('asset_code')) {
            $this->generateBarcodeImage($asset);
        }
        
        return redirect()->route('admin.assets.index')
                         ->with('success', 'Aset berhasil diperbarui');
    }

    /**
     * Remove the specified asset from storage.
     */
    public function destroy(Asset $asset)
    {
        // Check if asset has maintenance records
        if ($asset->maintenances()->count() > 0) {
            return redirect()->back()
                             ->with('error', 'Aset tidak dapat dihapus karena memiliki riwayat maintenance');
        }
        
        // Hapus spesifikasi terkait
        $asset->specifications()->delete();
        
        $asset->delete();
        
        return redirect()->route('admin.assets.index')
                         ->with('success', 'Aset berhasil dihapus');
    }

    // ============================================
    // SPESIFIKASI METHODS
    // ============================================

    /**
     * Simpan spesifikasi aset.
     */
    private function saveSpecifications(Asset $asset, array $specifications, Category $category)
    {
        foreach ($specifications as $key => $value) {
            // Cek apakah spesifikasi ini milik kategori yang dipilih
            $spec = $category->activeSpecifications()->where('key', $key)->first();
            
            if (!$spec) {
                continue; // Skip jika spesifikasi tidak valid untuk kategori ini
            }
            
            // Skip jika tidak required dan nilainya kosong
            if (!$spec->is_required && (is_null($value) || $value === '')) {
                continue;
            }
            
            // Simpan atau update spesifikasi
            AssetSpecification::updateOrCreate(
                [
                    'asset_id' => $asset->id,
                    'spec_key' => $key
                ],
                [
                    'spec_value' => $value
                ]
            );
        }
    }

    /**
     * Get specifications by category (AJAX).
     */
    public function getSpecificationsByCategory(Request $request)
    {
        $categoryId = $request->category_id;
        
        if (!$categoryId) {
            return response()->json(['specifications' => []]);
        }
        
        $category = Category::with(['activeSpecifications' => function($query) {
            $query->orderBy('sort_order');
        }])->find($categoryId);
        
        if (!$category) {
            return response()->json(['specifications' => []]);
        }
        
        $specifications = $category->activeSpecifications->map(function($spec) {
            return [
                'key' => $spec->key,
                'label' => $spec->label,
                'type' => $spec->type,
                'options' => $spec->options,
                'is_required' => $spec->is_required,
                'placeholder' => $spec->placeholder,
                'help_text' => $spec->help_text,
            ];
        });
        
        return response()->json([
            'success' => true,
            'specifications' => $specifications,
            'category_name' => $category->name
        ]);
    }

    /**
     * Generate QR Code image for asset.
     */
    private function generateBarcodeImage(Asset $asset)
    {
        try {
            // Ambil IP Address Server secara otomatis
            $serverIp = $this->getServerIp();
            $port = env('APP_PORT', 8080);
            
            // Buat URL lengkap dengan IP
            $url = "http://{$serverIp}:{$port}/admin/assets/{$asset->id}";
            
            // Generate QR Code sebagai SVG dengan URL
            $qrCode = QrCode::format('svg')
                            ->size(300)
                            ->errorCorrection('H')
                            ->margin(1)
                            ->generate($url);
            
            $filename = 'qrcodes/' . $asset->asset_code . '.svg';
            $path = storage_path('app/public/' . $filename);
            
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            
            file_put_contents($path, $qrCode);
            
            $asset->barcode_image = $filename;
            $asset->saveQuietly();
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('QR Code generation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get server IP address.
     */
    private function getServerIp()
    {
        // Coba dari server name
        if (isset($_SERVER['SERVER_ADDR'])) {
            return $_SERVER['SERVER_ADDR'];
        }
        
        // Coba dari hostname
        $hostname = gethostname();
        $ip = gethostbyname($hostname);
        
        if ($ip && $ip !== $hostname) {
            return $ip;
        }
        
        // Coba dari interface jaringan
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $ip = shell_exec("ip route get 1 | awk '{print $NF;exit}'");
            if ($ip) {
                return trim($ip);
            }
        }
        
        // Default localhost
        return '127.0.0.1';
    }

    /**
     * Get QR Code image for asset.
     */
    public function getBarcodeImage(Asset $asset)
    {
        // Cek di storage
        $storagePath = storage_path('app/public/' . $asset->barcode_image);
        
        if ($asset->barcode_image && file_exists($storagePath)) {
            return response()->file($storagePath, ['Content-Type' => 'image/png']);
        }
        
        // Generate QR Code langsung
        try {
            $qrCode = QrCode::format('png')
                            ->size(300)
                            ->errorCorrection('H')
                            ->margin(1)
                            ->generate($asset->asset_code);
            
            return response($qrCode)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'inline; filename="qrcode.png"');
        } catch (\Exception $e) {
            // Return placeholder jika gagal
            $svg = '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                <rect width="200" height="200" fill="#f0f0f0"/>
                <text x="100" y="110" text-anchor="middle" fill="#999" font-size="12" font-family="monospace">QR Code Error</text>
            </svg>';
            
            return response($svg)->header('Content-Type', 'image/svg+xml');
        }
    }

    /**
     * Generate unique asset code.
     */
    private function generateAssetCode(): string
    {
        $prefix = 'AST';
        $code = $prefix . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        
        // Ensure uniqueness
        while (Asset::where('asset_code', $code)->exists()) {
            $code = $prefix . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        }
        
        return $code;
    }

    /**
     * Generate QR Code for asset (AJAX).
     */
    public function generateBarcode(Asset $asset)
    {
        try {
            $this->generateBarcodeImage($asset);
            
            return response()->json([
                'success' => true,
                'message' => 'QR Code berhasil digenerate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate QR Code: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get asset by barcode (for scanner).
     */
    public function getAssetByBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);
        
        $asset = Asset::with(['category', 'location', 'assignedTo'])
                      ->where('asset_code', $request->barcode)
                      ->first();
        
        if (!$asset) {
            return response()->json([
                'success' => false,
                'message' => 'Aset tidak ditemukan'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'asset' => [
                'id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'name' => $asset->name,
                'serial_number' => $asset->serial_number,
                'status' => $asset->status,
                'status_label' => $asset->status_label,
                'status_badge_class' => $asset->status_badge_class,
                'location' => $asset->location ? [
                    'id' => $asset->location->id,
                    'name' => $asset->location->name,
                    'full_path' => $asset->location->full_path,
                ] : null,
                'assigned_to' => $asset->assignedTo ? [
                    'id' => $asset->assignedTo->id,
                    'name' => $asset->assignedTo->name,
                ] : null,
                'formatted_purchase_price' => $asset->formatted_purchase_price,
                'formatted_current_value' => $asset->formatted_current_value,
            ]
        ]);
    }

    /**
     * Print label for single asset.
     */
    public function printLabel(Asset $asset)
    {
        if (!$asset) {
            abort(404, 'Aset tidak ditemukan');
        }
        
        return view('admin.assets.print-label', compact('asset'));
    }

    /**
     * Print labels for multiple assets.
     */
    public function printLabels(Request $request)
    {
        $request->validate([
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'exists:assets,id'
        ]);
        
        $assetIds = $request->asset_ids;
        
        // Jika asset_ids berupa string JSON, decode dulu
        if (is_string($assetIds)) {
            $assetIds = json_decode($assetIds, true);
        }
        
        // Pastikan asset_ids adalah array
        if (!is_array($assetIds)) {
            $assetIds = [];
        }
        
        $assets = Asset::whereIn('id', $assetIds)->get();
        
        return view('admin.assets.print-batch', compact('assets'));
    }

    /**
     * Check in/out asset.
     */
    public function toggleCheckInOut(Request $request, Asset $asset)
    {
        // Ambil user dari request
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu'
            ]);
        }
        
        $userId = $user->id;
        
        if ($asset->status === Asset::STATUS_AVAILABLE) {
            $asset->status = Asset::STATUS_IN_USE;
            $asset->assigned_to = $userId;
            $message = 'Aset berhasil di-checkout';
        } elseif ($asset->status === Asset::STATUS_IN_USE) {
            // Cek apakah aset memang sedang dipegang oleh user yang sama
            if ($asset->assigned_to !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aset ini sedang digunakan oleh user lain'
                ]);
            }
            
            $asset->status = Asset::STATUS_AVAILABLE;
            $asset->assigned_to = null;
            $message = 'Aset berhasil di-checkin';
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Aset sedang dalam maintenance atau rusak, tidak dapat di-checkin/out'
            ]);
        }
        
        $asset->save();
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'asset' => [
                'id' => $asset->id,
                'status' => $asset->status,
                'status_label' => $asset->status_label,
                'status_badge_class' => $asset->status_badge_class,
            ]
        ]);
    }

    /**
     * Export assets to Excel.
     */
    public function export(Request $request)
    {
        try {
            $filters = [
                'category' => $request->category,
                'location' => $request->location,
                'status' => $request->status,
                'search' => $request->search,
            ];

            $fileName = 'aset_' . date('Y-m-d_His') . '.xlsx';
            
            return Excel::download(new AssetsExport($filters), $fileName);
            
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    /**
     * Export amortization to Excel.
     */
    public function exportAmortization()
    {
        try {
            $fileName = 'amortisasi_aset_' . date('Y-m-d_His') . '.xlsx';
            
            return Excel::download(new AmortizationExport(), $fileName);
            
        } catch (\Exception $e) {
            Log::error('Export amortization error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    /**
     * Download template import aset.
     */
    public function downloadTemplate()
    {
        return Excel::download(new ImportTemplateExport(), 'template_import_aset.xlsx');
    }

    /**
     * Show import form.
     */
    public function showImportForm()
    {
        return view('admin.assets.import');
    }

    /**
     * Import assets from Excel.
     */
    public function import(Request $request)
    {
        set_time_limit(300); // Set timeout 5 menit
        
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Maks 10MB
        ]);

        try {
            $import = new AssetsImport();
            Excel::import($import, $request->file('file'));
            
            $rowCount = $import->getRowCount();
            $successCount = $import->getSuccessCount();
            $failures = $import->getFailures();
            
            $message = "Import selesai. Total data: {$rowCount}, Berhasil: {$successCount}, Gagal: " . count($failures);
            
            if (count($failures) > 0) {
                session()->flash('import_errors', $failures);
                return redirect()->back()->with('warning', $message);
            }
            
            return redirect()->route('admin.assets.index')->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}