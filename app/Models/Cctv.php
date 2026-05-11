<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cctv extends Model
{
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
}