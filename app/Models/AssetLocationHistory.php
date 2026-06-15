<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetLocationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id', 'old_location_id', 'new_location_id', 
        'old_location_name', 'new_location_name', 'changed_by', 
        'reason', 'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function oldLocation()
    {
        return $this->belongsTo(Location::class, 'old_location_id');
    }

    public function newLocation()
    {
        return $this->belongsTo(Location::class, 'new_location_id');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Helper untuk format display
    public function getMovementIconAttribute()
    {
        if (!$this->old_location_id && $this->new_location_id) {
            return '<i class="bi bi-plus-circle text-success"></i>';
        }
        if ($this->old_location_id && !$this->new_location_id) {
            return '<i class="bi bi-dash-circle text-danger"></i>';
        }
        return '<i class="bi bi-arrow-right-short text-primary"></i>';
    }

    public function getMovementTextAttribute()
    {
        $old = $this->old_location_name ?? '-';
        $new = $this->new_location_name ?? '-';
        
        if (!$this->old_location_id && $this->new_location_id) {
            return "<span class='text-success'>Aset ditempatkan di {$new}</span>";
        }
        if ($this->old_location_id && !$this->new_location_id) {
            return "<span class='text-danger'>Aset dikeluarkan dari {$old}</span>";
        }
        return "<span>{$old} → {$new}</span>";
    }
}