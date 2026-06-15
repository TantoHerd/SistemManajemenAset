<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceReminder extends Model
{
    use HasFactory;

    protected $fillable = ['maintenance_id', 'days_before', 'sent_at', 'sent_to', 'status', 'error_message'];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }
}