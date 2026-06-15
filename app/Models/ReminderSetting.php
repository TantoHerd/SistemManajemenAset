<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderSetting extends Model
{
    use HasFactory;

    protected $fillable = ['is_active', 'reminder_days', 'email_notification', 'system_notification', 'send_time'];

    protected $casts = [
        'is_active' => 'boolean',
        'reminder_days' => 'array',
        'email_notification' => 'boolean',
        'system_notification' => 'boolean',
        'send_time' => 'datetime:H:i',
    ];

    public function getFormattedDaysAttribute()
    {
        $days = $this->reminder_days ?? [];
        $labels = [];
        foreach ($days as $day) {
            $labels[] = "H-{$day}";
        }
        return implode(', ', $labels);
    }
}