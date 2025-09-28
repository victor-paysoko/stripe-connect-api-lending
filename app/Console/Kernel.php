<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Sync payment status every 30 minutes
        $schedule->command('payments:sync-status')->everyThirtyMinutes();

        // Cleanup webhook events daily
        $schedule->command('webhooks:cleanup')->daily();

        // Retry failed payments hourly
        $schedule->command('payments:retry-failed')->hourly();
    }
}
