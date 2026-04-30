<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'icon',
        'color',
        'link',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // Constants untuk type
    const TYPE_WARRANTY_EXPIRING = 'warranty_expiring';
    const TYPE_MAINTENANCE_DUE = 'maintenance_due';
    const TYPE_ASSET_OVERDUE = 'asset_overdue';
    const TYPE_MAINTENANCE_COMPLETED = 'maintenance_completed';
    const TYPE_ASSET_CHECKED_OUT = 'asset_checked_out';
    const TYPE_ASSET_CHECKED_IN = 'asset_checked_in';
    const TYPE_SYSTEM = 'system';

    // Icon & color mapping
    public static function getTypeConfig($type)
    {
        return [
            self::TYPE_WARRANTY_EXPIRING => ['icon' => 'shield-exclamation', 'color' => 'warning'],
            self::TYPE_MAINTENANCE_DUE => ['icon' => 'wrench', 'color' => 'info'],
            self::TYPE_ASSET_OVERDUE => ['icon' => 'exclamation-triangle', 'color' => 'danger'],
            self::TYPE_MAINTENANCE_COMPLETED => ['icon' => 'check-circle', 'color' => 'success'],
            self::TYPE_ASSET_CHECKED_OUT => ['icon' => 'box-arrow-right', 'color' => 'primary'],
            self::TYPE_ASSET_CHECKED_IN => ['icon' => 'box-arrow-in-left', 'color' => 'success'],
            self::TYPE_SYSTEM => ['icon' => 'bell', 'color' => 'secondary'],
        ][$type] ?? ['icon' => 'bell', 'color' => 'primary'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk unread
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // Scope untuk user tertentu
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Mark as read
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    // Helper: Buat notifikasi
    public static function createNotification($userId, $type, $title, $message, $link = null)
    {
        $config = self::getTypeConfig($type);
        
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $config['icon'],
            'color' => $config['color'],
            'link' => $link
        ]);
    }
}