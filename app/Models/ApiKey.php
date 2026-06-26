<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'key', 'allowed_ips', 'expires_at', 'last_used_at', 'is_active'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'is_active' => 'boolean',
        'allowed_ips' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateKey()
    {
        return 'simaset_' . Str::random(32);
    }

    public function isValid($ip = null)
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($ip && $this->allowed_ips && !in_array($ip, $this->allowed_ips)) return false;
        return true;
    }
}