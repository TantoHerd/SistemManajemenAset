<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AutoMaintenanceNotification extends Notification
{
    use Queueable;
    
    protected $asset;
    protected $maintenance;
    
    public function __construct($asset, $maintenance)
    {
        $this->asset = $asset;
        $this->maintenance = $maintenance;
    }
    
    public function via($notifiable)
    {
        return ['database'];
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $notifiable->id,
            'title' => 'Maintenance Otomatis Dijadwalkan',
            'message' => "Maintenance rutin untuk aset '{$this->asset->name}' telah dijadwalkan",
            'type' => 'auto_maintenance',
            'icon' => 'bi-calendar-check',
            'color' => 'info',
            'link' => route('admin.maintenances.show', $this->maintenance),
            'is_read' => 0,
            'read_at' => null,
        ];
    }
}