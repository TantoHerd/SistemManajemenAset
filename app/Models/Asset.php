<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Traits\AuditTrait;

class Asset extends Model
{
    use HasFactory;
    use AuditTrait;

    /**
     * Status constants
     */
    const STATUS_AVAILABLE = 'available';
    const STATUS_IN_USE = 'in_use';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_DAMAGED = 'damaged';
    const STATUS_DISPOSED = 'disposed';

    /**
     * All available statuses
     */
    public static $statuses = [
        self::STATUS_AVAILABLE => 'Tersedia',
        self::STATUS_IN_USE => 'Sedang Dipakai',
        self::STATUS_MAINTENANCE => 'Perbaikan',
        self::STATUS_DAMAGED => 'Rusak',
        self::STATUS_DISPOSED => 'Dihapuskan',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'asset_code',
        'name',
        'serial_number',
        'model',
        'brand',
        'category_id',
        'location_id',
        'assigned_to',
        'status',
        'purchase_date',
        'purchase_price',
        'residual_value',
        'useful_life_months',
        'current_value',
        'barcode_image',
        'notes',
        'warranty_expiry',
        'auto_maintenance_frequency',
        'last_maintenance_date',
        'next_maintenance_date',
        'auto_maintenance_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_price' => 'decimal:2',
        'residual_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'useful_life_months' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'auto_maintenance_active' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status_label',
        'status_badge_class',
        'formatted_purchase_price',
        'formatted_current_value',
        'depreciation_percentage',
        'is_under_warranty',
    ];

    /**
     * Get the category that owns the asset.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the location where the asset is placed.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Get the user who is assigned this asset.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get all maintenance records for this asset.
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class, 'asset_id');
    }

    /**
     * Get status label in Indonesian.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::$statuses[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Get Bootstrap badge class for status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_AVAILABLE => 'bg-label-success',
            self::STATUS_IN_USE => 'bg-label-primary',
            self::STATUS_MAINTENANCE => 'bg-label-warning',
            self::STATUS_DAMAGED => 'bg-label-danger',
            self::STATUS_DISPOSED => 'bg-label-secondary',
            default => 'bg-label-info',
        };
    }

    /**
     * Get formatted purchase price (Rp 10.000.000).
     */
    public function getFormattedPurchasePriceAttribute(): string
    {
        return 'Rp ' . number_format($this->purchase_price, 0, ',', '.');
    }

    /**
     * Get formatted current value (Rp 10.000.000).
     */
    public function getFormattedCurrentValueAttribute(): string
    {
        return 'Rp ' . number_format($this->current_value, 0, ',', '.');
    }

    /**
     * Get depreciation percentage.
     */
    public function getDepreciationPercentageAttribute(): float
    {
        if ($this->purchase_price <= 0) {
            return 0;
        }
        
        $depreciatedAmount = $this->purchase_price - $this->current_value;
        return round(($depreciatedAmount / $this->purchase_price) * 100, 2);
    }

    /**
     * Check if asset is under warranty.
     */
    public function getIsUnderWarrantyAttribute(): bool
    {
        if (!$this->warranty_expiry) {
            return false;
        }
        
        return Carbon::now()->lessThanOrEqualTo($this->warranty_expiry);
    }

    /**
     * Calculate monthly depreciation.
     */
    public function calculateMonthlyDepreciation(): float
    {
        if ($this->useful_life_months <= 0) {
            return 0;
        }
        
        return ($this->purchase_price - $this->residual_value) / $this->useful_life_months;
    }

    /**
     * Update current value based on purchase date.
     */
    public function updateCurrentValue(): void
    {
        $monthsPassed = Carbon::parse($this->purchase_date)->diffInMonths(Carbon::now());
        
        if ($monthsPassed >= $this->useful_life_months) {
            $this->current_value = $this->residual_value;
        } else {
            $monthlyDepreciation = $this->calculateMonthlyDepreciation();
            $depreciatedAmount = $monthlyDepreciation * $monthsPassed;
            $this->current_value = max($this->residual_value, $this->purchase_price - $depreciatedAmount);
        }
        
        $this->saveQuietly();
    }

    /**
     * Scope a query to only include assets of a given status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include available assets.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    /**
     * Scope a query to only include assets in use.
     */
    public function scopeInUse($query)
    {
        return $query->where('status', self::STATUS_IN_USE);
    }

    /**
     * Scope a query to only include assets under maintenance.
     */
    public function scopeUnderMaintenance($query)
    {
        return $query->where('status', self::STATUS_MAINTENANCE);
    }

    /**
     * Scope a query to search assets.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('asset_code', 'like', "%{$search}%")
                     ->orWhere('name', 'like', "%{$search}%")
                     ->orWhere('serial_number', 'like', "%{$search}%");
    }

    /**
     * Generate barcode image for asset.
     */
    private function generateBarcodeImage(Asset $asset)
    {
        try {
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode(
                $asset->asset_code,
                $generator::TYPE_CODE_128,
                2,
                60
            );
            
            $filename = 'barcodes/' . $asset->asset_code . '.png';
            $path = storage_path('app/public/' . $filename);
            
            // Create directory if not exists
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            
            file_put_contents($path, $barcode);
            
            $asset->barcode_image = $filename;
            $asset->saveQuietly();
            
        } catch (\Exception $e) {
            Log::error('Barcode generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate barcode for asset (AJAX).
     */
    public function generateBarcode(Asset $asset)
    {
        try {
            $this->generateBarcodeImage($asset);
            
            return response()->json([
                'success' => true,
                'message' => 'Barcode berhasil digenerate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate barcode: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get barcode image for asset.
     */
    public function getBarcodeImage(Asset $asset)
    {
        // Cek di storage
        $storagePath = storage_path('app/public/' . $asset->barcode_image);
        
        if ($asset->barcode_image && file_exists($storagePath)) {
            return response()->file($storagePath, ['Content-Type' => 'image/png']);
        }
        
        // Cek di public/storage
        $publicPath = public_path('storage/' . $asset->barcode_image);
        if ($asset->barcode_image && file_exists($publicPath)) {
            return response()->file($publicPath, ['Content-Type' => 'image/png']);
        }
        
        // Generate temporary barcode if not exists
        try {
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode(
                $asset->asset_code,
                $generator::TYPE_CODE_128,
                2,
                60
            );
            
            return response($barcode)->header('Content-Type', 'image/png');
        } catch (\Exception $e) {
            // Return placeholder image
            $placeholder = '<svg width="200" height="60" xmlns="http://www.w3.org/2000/svg">
                <rect width="200" height="60" fill="#f0f0f0"/>
                <text x="100" y="35" text-anchor="middle" fill="#999" font-size="12">' . $asset->asset_code . '</text>
            </svg>';
            
            return response($placeholder)->header('Content-Type', 'image/svg+xml');
        }
    }

    /**
     * Print label for single asset.
     */
    public function printLabel(Asset $asset)
    {
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
        
        $assetIds = json_decode($request->asset_ids, true);
        $assets = Asset::whereIn('id', $assetIds)->get();
        
        return view('admin.assets.print-batch', compact('assets'));
    }

    public function specifications()
    {
        return $this->hasMany(AssetSpecification::class);
    }

    // Get single specification value
    public function getSpecification($key, $default = null)
    {
        if (!$this->relationLoaded('specifications')) {
            $this->load('specifications');
        }
        
        return $this->specifications
            ->where('spec_key', $key)
            ->first()?->spec_value ?? $default;
    }

    // Get all specifications as key-value array
    public function getAllSpecifications(): array
    {
        if (!$this->relationLoaded('specifications')) {
            $this->load('specifications');
        }
        
        return $this->specifications->pluck('spec_value', 'spec_key')->toArray();
    }

    // Set multiple specifications at once
    public function setSpecifications(array $specs): void
    {
        AssetSpecification::setSpecifications($this, $specs);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AssetDocument::class);
    }

    public function cctvs()
    {
        return $this->hasMany(Cctv::class);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate asset code if not provided
        static::creating(function ($asset) {
            if (empty($asset->asset_code)) {
                $asset->asset_code = 'AST-' . strtoupper(uniqid());
            }
            
            // Set default useful life from category if not set
            if (empty($asset->useful_life_months) && $asset->category) {
                $asset->useful_life_months = $asset->category->useful_life_months;
            }
            
            // Set initial current value if not set
            if (empty($asset->current_value)) {
                $asset->current_value = $asset->purchase_price;
            }
        });
    }
    
    protected function getModuleName()
    {
        return 'asset';
    }

    protected function getRecordName()
    {
        return $this->name . ' (' . $this->asset_code . ')';
    }

    // Record location change
    public function recordLocationChange($oldLocationId, $newLocationId, $reason = null, $notes = null)
    {
        $oldLocation = $oldLocationId ? Location::find($oldLocationId) : null;
        $newLocation = $newLocationId ? Location::find($newLocationId) : null;

        return AssetLocationHistory::create([
            'asset_id' => $this->id,
            'old_location_id' => $oldLocationId,
            'new_location_id' => $newLocationId,
            'old_location_name' => $oldLocation?->name,
            'new_location_name' => $newLocation?->name,
            'changed_by' => auth()->id(),
            'reason' => $reason,
            'notes' => $notes,
        ]);
    }

    // Get location history
    public function locationHistory()
    {
        return $this->hasMany(AssetLocationHistory::class)->latest();
    }

    // Override update untuk auto record
    protected static function booted()
    {
        static::updated(function ($asset) {
            if ($asset->isDirty('location_id')) {
                $oldLocationId = $asset->getOriginal('location_id');
                $newLocationId = $asset->location_id;
                
                $asset->recordLocationChange($oldLocationId, $newLocationId, 'Update dari form aset');
            }
        });
    }

    // Hitung next maintenance date
    public function calculateNextMaintenanceDate()
    {
        if (!$this->auto_maintenance_active || $this->auto_maintenance_frequency == 'none') {
            return null;
        }
        
        $lastDate = $this->last_maintenance_date 
            ? \Carbon\Carbon::parse($this->last_maintenance_date)
            : \Carbon\Carbon::parse($this->purchase_date ?? now());
        
        switch ($this->auto_maintenance_frequency) {
            case 'monthly': return $lastDate->addMonth();
            case 'quarterly': return $lastDate->addMonths(3);
            case 'semi_annual': return $lastDate->addMonths(6);
            case 'annual': return $lastDate->addYear();
            default: return null;
        }
    }

    // Create auto maintenance
    public function createAutoMaintenance()
    {
        Log::info('Auto Maintenance Check', [
            'asset_id' => $this->id,
            'active' => $this->auto_maintenance_active,
            'next_date' => $this->next_maintenance_date,
            'now' => now(),
            'condition' => $this->next_maintenance_date <= now()
        ]);
        
        if (!$this->auto_maintenance_active || !$this->next_maintenance_date) {
            return null;
        }
        
        if ($this->next_maintenance_date <= now()) {
            $frequencyLabels = [
                'monthly' => 'Bulanan',
                'quarterly' => '3 Bulanan',
                'semi_annual' => '6 Bulanan',
                'annual' => 'Tahunan',
            ];
            
            $frequencyLabel = $frequencyLabels[$this->auto_maintenance_frequency] ?? ucfirst($this->auto_maintenance_frequency);
            
            $maintenance = Maintenance::create([
                'asset_id' => $this->id,
                'title' => 'Maintenance Rutin - ' . $frequencyLabel,
                'maintenance_date' => now(),
                'status' => 'pending',  // Gunakan 'pending' jika enum tidak punya 'scheduled'
                'technician' => null,
                'cost' => 0,
                'description' => "Maintenance otomatis untuk aset {$this->name}",
            ]);
            
            $this->update([
                'last_maintenance_date' => now(),
                'next_maintenance_date' => $this->calculateNextMaintenanceDate(),
            ]);
            
            return $maintenance;
        }
        
        return null;
    }
}