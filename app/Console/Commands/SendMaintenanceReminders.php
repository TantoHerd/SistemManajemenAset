<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Maintenance;
use App\Models\ReminderSetting;
use App\Models\MaintenanceReminder;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MaintenanceReminderNotification;
use Carbon\Carbon;

class SendMaintenanceReminders extends Command
{
    protected $signature = 'reminder:send-maintenance';
    protected $description = 'Send maintenance reminders based on schedule';

    public function handle()
    {
        $settings = ReminderSetting::first();

        if (!$settings || !$settings->is_active) {
            $this->info('Reminder is disabled.');
            return;
        }

        $today = Carbon::today();
        $reminderDays = $settings->reminder_days ?? [7, 3, 1];
        $sendTime = $settings->send_time ?? '08:00:00';

        // Cek apakah sudah waktunya kirim
        if (Carbon::now()->format('H:i') < $sendTime) {
            $this->info('Not yet time to send reminders.');
            return;
        }

        $totalSent = 0;

        foreach ($reminderDays as $days) {
            $targetDate = $today->copy()->addDays($days);
            
            $maintenances = Maintenance::whereDate('maintenance_date', $targetDate)
                ->where('status', '!=', 'completed')
                ->get();

            foreach ($maintenances as $maintenance) {
                // Cek apakah sudah pernah dikirim
                $existing = MaintenanceReminder::where('maintenance_id', $maintenance->id)
                    ->where('days_before', $days)
                    ->first();

                if ($existing && $existing->status == 'sent') {
                    continue;
                }

                // Kirim reminder
                $this->sendReminder($maintenance, $days, $settings);
                $totalSent++;
            }
        }

        $this->info("Sent {$totalSent} maintenance reminders.");
    }

    private function sendReminder($maintenance, $daysBefore, $settings)
    {
        // Dapatkan user yang bertanggung jawab
        $responsibleUsers = User::role(['admin', 'technician'])->get();
        
        $maintenanceDate = Carbon::parse($maintenance->maintenance_date);
        $reminder = null;

        foreach ($responsibleUsers as $user) {
            $sentTo = [];
            
            // Kirim notifikasi sistem (Bell)
            if ($settings->system_notification) {
                $user->notify(new MaintenanceReminderNotification($maintenance, $daysBefore));
                $sentTo[] = 'system';
            }

            // Kirim email
            if ($settings->email_notification && $user->email) {
                try {
                    Mail::send('emails.maintenance-reminder', [
                        'user' => $user,
                        'maintenance' => $maintenance,
                        'daysBefore' => $daysBefore,
                        'date' => $maintenanceDate->format('d/m/Y')
                    ], function ($message) use ($user, $maintenance, $daysBefore) {
                        $message->to($user->email)
                                ->subject("Reminder Maintenance: {$maintenance->title} (H-{$daysBefore})");
                    });
                    $sentTo[] = 'email';
                } catch (\Exception $e) {
                    // Log error
                }
            }

            // Simpan log reminder
            if (!empty($sentTo)) {
                $reminder = MaintenanceReminder::updateOrCreate(
                    [
                        'maintenance_id' => $maintenance->id,
                        'days_before' => $daysBefore,
                    ],
                    [
                        'sent_at' => now(),
                        'sent_to' => implode(', ', $sentTo),
                        'status' => 'sent',
                    ]
                );
            }
        }

        return $reminder;
    }
}