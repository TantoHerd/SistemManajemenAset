<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditTrait;

class Cctv extends Model
{
    use AuditTrait;

    protected $fillable = [
        'name', 'ip_address', 'port', 'username', 'password',
        'brand', 'model', 'stream_url', 'snapshot_url',
        'location', 'status', 'notes'
    ];

    protected $casts = [
        'port' => 'integer',
    ];

    // Helper: URL stream
    public function getStreamUrlAttribute($value)
    {
        return $value ?: "http://{$this->ip_address}:{$this->port}";
    }

    // Helper: URL snapshot
    public function getSnapshotUrlAttribute($value)
    {
        return $value ?: "http://{$this->ip_address}:{$this->port}/snapshot.jpg";
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
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