<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class CheckNotifications extends Command
{
    protected $signature = 'notifications:check';
    protected $description = 'Check and generate notifications for warranty, maintenance, and overdue assets';

    public function handle()
    {
        $this->info('Checking notifications...');
        
        NotificationService::checkAndGenerate();
        
        $this->info('Notifications checked successfully!');
    }
}