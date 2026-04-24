<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'parent_id',
        'building',
        'floor',
        'room',
        'address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'parent_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent location.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    /**
     * Get the child locations.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    /**
     * Get all assets in this location.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'location_id');
    }

    /**
     * Get the full location path (e.g., "Gedung A > Lantai 1 > IT Department").
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }

    /**
     * Get formatted location string (building - floor - room).
     */
    public function getFormattedLocationAttribute(): string
    {
        $parts = [];
        
        if ($this->building) {
            $parts[] = $this->building;
        }
        if ($this->floor) {
            $parts[] = "Lantai {$this->floor}";
        }
        if ($this->room) {
            $parts[] = $this->room;
        }
        
        return implode(' - ', $parts) ?: $this->name;
    }

    /**
     * Scope to get only top-level locations.
     */
    public function scopeRootLocations($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get locations by building.
     */
    public function scopeByBuilding($query, $building)
    {
        return $query->where('building', $building);
    }
}