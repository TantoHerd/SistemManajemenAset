<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditTrait;

class StockOpnameSession extends Model
{
    use HasFactory;
    use AuditTrait;

    protected $table = 'stock_opname_sessions';

    protected $fillable = [
        'name', 'location_id', 'status', 'notes', 
        'created_by', 'started_at', 'completed_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class, 'session_id');
    }

    public function getProgressAttribute()
    {
        $total = $this->items()->count();
        if ($total == 0) return 0;
        
        $scanned = $this->items()->whereNotNull('scanned_at')->count();
        return round(($scanned / $total) * 100);
    }

    public function getSummaryAttribute()
    {
        $items = $this->items;
        
        return [
            'total' => $items->count(),
            'found' => $items->where('actual_status', 'found')->count(),
            'missing' => $items->where('actual_status', 'missing')->count(),
            'damaged' => $items->where('actual_status', 'damaged')->count(),
            'moved' => $items->where('actual_status', 'moved')->count(),
            'scanned' => $items->whereNotNull('scanned_at')->count(),
        ];
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