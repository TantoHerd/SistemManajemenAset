<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditTrait;

class StockOpnameItem extends Model
{
    use HasFactory;
    use AuditTrait;

    protected $fillable = [
        'session_id', 'asset_id', 'expected_location', 'actual_status', 
        'actual_location', 'notes', 'scanned_by', 'scanned_at'
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(StockOpnameSession::class, 'session_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->actual_status) {
            'found' => '<span class="badge bg-success">Ditemukan</span>',
            'missing' => '<span class="badge bg-danger">Hilang</span>',
            'damaged' => '<span class="badge bg-warning">Rusak</span>',
            'moved' => '<span class="badge bg-info">Berpindah</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
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