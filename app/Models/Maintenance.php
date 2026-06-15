<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditTrait;

class Maintenance extends Model
{
    use HasFactory;
    use AuditTrait;

    protected $table = 'maintenances';

    protected $fillable = [
        'asset_id',
        'title',
        'description',
        'maintenance_date',
        'technician',
        'cost',
        'status',
        'notes',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public static $statuses = [
        'pending' => 'Pending',
        'in_progress' => 'Dalam Proses',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function getStatusLabelAttribute()
    {
        return self::$statuses[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    public function getFormattedCostAttribute()
    {
        return 'Rp ' . number_format($this->cost, 0, ',', '.');
    }

    // Perbaiki accessor untuk maintenance_date
    public function getFormattedDateAttribute()
    {
        return $this->maintenance_date ? $this->maintenance_date->format('d/m/Y') : '-';
    }

    protected function getModuleName()
    {
        return 'asset';
    }

    protected function getRecordName()
    {
        return $this->name . ' (' . $this->asset_code . ')';
    }
}