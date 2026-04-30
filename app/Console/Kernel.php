protected function commands(): void
{
    $this->load(__DIR__.'/Commands');

    require base_path('routes/console.php');
}

protected function schedule(Schedule $schedule): void
{
    // Cek notifikasi setiap hari jam 8 pagi
    $schedule->command('notifications:check')->dailyAt('08:00');
}