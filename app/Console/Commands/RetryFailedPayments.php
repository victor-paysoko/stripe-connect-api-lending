<?php
// app/Console/Commands/RetryFailedPayments.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RepaymentAttempt;

class RetryFailedPayments extends Command
{
    protected $signature = 'payments:retry-failed
                            {--fi= : Financial Institution ID}
                            {--hours=24 : Retry failures from last N hours}';

    protected $description = 'Notify customers about failed payments and suggest retry';

    public function handle()
    {
        $query = RepaymentAttempt::failed()
            ->where('created_at', '>=', now()->subHours($this->option('hours')));

        if ($this->option('fi')) {
            $query->forFinancialInstitution($this->option('fi'));
        }

        $failedAttempts = $query->get();
        $this->info("Found {$failedAttempts->count()} failed payment attempts to process.");

        foreach ($failedAttempts as $attempt) {
            // Here you would implement your notification logic
            // Email, SMS, or in-app notification to retry payment

            $this->info("Notifying borrower for failed payment: {$attempt->id}");
            // $this->notifyBorrower($attempt);
        }

        $this->info("Notification process completed.");
    }
}
