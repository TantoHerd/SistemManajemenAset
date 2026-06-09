<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupSetting extends Model
{
    use HasFactory;

    protected $table = 'backup_settings';

    protected $fillable = [
        'frequency',
        'time',
        'keep_backups',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'time' => 'datetime:H:i',
    ];

    // Helper methods
    public function getFrequencyLabelAttribute()
    {
        return match($this->frequency) {
            'daily' => 'Setiap Hari',
            'weekly' => 'Setiap Minggu',
            'monthly' => 'Setiap Bulan',
            default => ucfirst($this->frequency),
        };
    }

    public function getCronExpressionAttribute()
    {
        $timeParts = explode(':', $this->time);
        $minute = $timeParts[1] ?? '0';
        $hour = $timeParts[0] ?? '0';
        
        return match($this->frequency) {
            'daily' => "{$minute} {$hour} * * *",
            'weekly' => "{$minute} {$hour} * * 0", // Sunday
            'monthly' => "{$minute} {$hour} 1 * *", // First day of month
            default => "{$minute} {$hour} * * *",
        };
    }
}