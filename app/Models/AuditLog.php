<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id', 'username', 'action', 'module', 'record_id', 'record_name',
        'old_data', 'new_data', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper untuk format aksi
    public function getActionBadgeAttribute()
    {
        return match($this->action) {
            'create' => '<span class="badge bg-success">Create</span>',
            'read' => '<span class="badge bg-info">Read</span>',
            'update' => '<span class="badge bg-warning">Update</span>',
            'delete' => '<span class="badge bg-danger">Delete</span>',
            'login' => '<span class="badge bg-primary">Login</span>',
            'logout' => '<span class="badge bg-secondary">Logout</span>',
            'export' => '<span class="badge bg-dark">Export</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->action) . '</span>',
        };
    }

    // Helper untuk format module
    public function getModuleBadgeAttribute()
    {
        $colors = [
            'asset' => 'primary',
            'maintenance' => 'warning',
            'loan' => 'info',
            'user' => 'danger',
            'cctv' => 'success',
            'mecard' => 'secondary',
            'stock_opname' => 'dark',
            'backup' => 'danger',
        ];

        $color = $colors[$this->module] ?? 'secondary';
        $labels = [
            'asset' => 'Aset',
            'maintenance' => 'Maintenance',
            'loan' => 'Peminjaman',
            'user' => 'User',
            'cctv' => 'CCTV',
            'mecard' => 'MeCard',
            'stock_opname' => 'Stock Opname',
            'backup' => 'Backup',
        ];

        $label = $labels[$this->module] ?? ucfirst($this->module);

        return "<span class='badge bg-{$color}'>{$label}</span>";
    }
}