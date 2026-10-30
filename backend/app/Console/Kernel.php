<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Ping /health every 14 minutes to prevent Render free-tier cold starts.
        // Render spins down inactive services after 15 minutes of no traffic;
        // pinging at 14-minute intervals keeps the instance warm at all times.
        $schedule->command('app:keep-alive')->everyFourteenMinutes();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
