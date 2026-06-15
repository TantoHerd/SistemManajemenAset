<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\Maintenance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessAutoMaintenance extends Command
{
    protected $signature = 'maintenance:auto-process';
    protected $description = 'Process automatic maintenance schedules';

    public function handle()
    {
        $this->info('Processing auto maintenance...');
        
        $assets = Asset::where('auto_maintenance_active', true)
            ->whereNotNull('next_maintenance_date')
            ->where('next_maintenance_date', '<=', Carbon::today())
            ->get();
        
        $created = 0;
        
        foreach ($assets as $asset) {
            $maintenance = $asset->createAutoMaintenance();
            
            if ($maintenance) {
                $created++;
                $this->info("Created maintenance for asset: {$asset->name}");
                
                // Buat notifikasi manual
                $this->createNotification($asset, $maintenance);
            }
        }
        
        $this->info("Created {$created} auto maintenance schedules.");
    }
    
    private function createNotification($asset, $maintenance)
    {
        // Ambil user dengan role technician, admin, dan super_admin
        $users = User::role(['technician', 'admin', 'super_admin'])->get();
        
        foreach ($users as $user) {
            DB::table('notifications')->insert([
                'user_id' => $user->id,
                'title' => 'Maintenance Otomatis Dijadwalkan',
                'message' => "Maintenance rutin untuk aset '{$asset->name}' telah dijadwalkan pada tanggal " . $maintenance->maintenance_date->format('d/m/Y'),
                'type' => 'maintenance_due',
                'icon' => 'bi-calendar-check',
                'color' => 'info',
                'link' => route('admin.maintenances.show', $maintenance),
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $this->info("Notifikasi dikirim ke {$users->count()} user");
    }
}