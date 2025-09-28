<?php
// app/Console/Commands/CleanupWebhookEvents.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProcessedWebhookEvent;

class CleanupWebhookEvents extends Command
{
    protected $signature = 'webhooks:cleanup {--days=30 : Remove events older than N days}';

    protected $description = 'Clean up old processed webhook events';

    public function handle()
    {
        $cutoffDate = now()->subDays($this->option('days'));

        $deleted = ProcessedWebhookEvent::where('processed_at', '<', $cutoffDate)->delete();

        $this->info("Deleted {$deleted} webhook events older than {$this->option('days')} days.");
    }
}
