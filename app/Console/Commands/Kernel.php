<?php 

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Run NewsAPI fetch every day at 12:00 PM
        $schedule->command('fetch:newsapi')->dailyAt('12:00');

        // Run Guardian fetch every day at 12:00 PM
        $schedule->command('fetch:guardian')->dailyAt('12:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
