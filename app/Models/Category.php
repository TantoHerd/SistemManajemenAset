<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
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
        'description',
        'useful_life_months',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'useful_life_months' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all assets in this category.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'category_id');
    }

    /**
     * Get formatted useful life in years.
     */
    public function getUsefulLifeInYearsAttribute(): float
    {
        return round($this->useful_life_months / 12, 1);
    }

    /**
     * Scope a query to only include active categories with assets.
     */
    public function scopeWithAssets($query)
    {
        return $query->has('assets');
    }
}