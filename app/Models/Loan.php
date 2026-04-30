<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'loan_code',
        'asset_id',
        'user_id',
        'approved_by',
        'loan_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'purpose',
        'notes',
        'condition_before',
        'condition_after',
        'fine_amount',
        'fine_paid'
    ];

    protected $casts = [
        'loan_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'fine_amount' => 'decimal:2',
        'fine_paid' => 'boolean'
    ];

    // Constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ACTIVE = 'active';
    const STATUS_RETURNED = 'returned';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CANCELLED = 'cancelled';

    // Relasi
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper: Generate kode pinjaman
    public static function generateCode(): string
    {
        $prefix = 'LOAN';
        $code = $prefix . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        
        while (self::where('loan_code', $code)->exists()) {
            $code = $prefix . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        }
        
        return $code;
    }

    // Helper: Status label & badge
    public function getStatusLabelAttribute(): string
    {
        return [
            self::STATUS_PENDING => 'Menunggu Approval',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_ACTIVE => 'Sedang Dipinjam',
            self::STATUS_RETURNED => 'Dikembalikan',
            self::STATUS_OVERDUE => 'Terlambat',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ][$this->status] ?? 'Unknown';
    }

    public function getStatusBadgeAttribute(): string
    {
        return [
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_ACTIVE => 'primary',
            self::STATUS_RETURNED => 'success',
            self::STATUS_OVERDUE => 'danger',
            self::STATUS_CANCELLED => 'secondary',
        ][$this->status] ?? 'secondary';
    }

    // Helper: Hitung denda keterlambatan (Rp 10.000/hari)
    public function calculateFine(): float
    {
        if (!$this->actual_return_date || !$this->expected_return_date) return 0;
        if ($this->actual_return_date->lte($this->expected_return_date)) return 0;

        $daysLate = $this->actual_return_date->diffInDays($this->expected_return_date);
        return $daysLate * 10000; // Rp 10.000 per hari
    }

    // Helper: Cek overdue
    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               now()->startOfDay()->gt($this->expected_return_date);
    }
}