<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class MaintenanceReminderNotification extends Notification
{
    use Queueable;

    protected $maintenance;
    protected $daysBefore;

    public function __construct($maintenance, $daysBefore)
    {
        $this->maintenance = $maintenance;
        $this->daysBefore = $daysBefore;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Reminder Maintenance',
            'message' => "Maintenance '{$this->maintenance->title}' akan dilaksanakan dalam H-{$this->daysBefore} pada tanggal " . \Carbon\Carbon::parse($this->maintenance->maintenance_date)->format('d/m/Y'),
            'url' => route('admin.maintenances.show', $this->maintenance),
            'type' => 'maintenance_reminder',
            'maintenance_id' => $this->maintenance->id,
        ];
    }
}