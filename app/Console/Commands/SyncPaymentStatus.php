<?php
// app/Console/Commands/SyncPaymentStatus.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RepaymentAttempt;
use Stripe\StripeClient;

class SyncPaymentStatus extends Command
{
    protected $signature = 'payments:sync-status
                            {--fi= : Financial Institution ID}
                            {--hours=24 : Sync attempts from last N hours}';

    protected $description = 'Sync repayment attempt statuses from Stripe';

    public function handle()
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        $query = RepaymentAttempt::where('created_at', '>=', now()->subHours($this->option('hours')))
            ->whereNotNull('stripe_payment_intent_id')
            ->whereIn('status', [
                RepaymentAttempt::STATUS_PENDING,
                RepaymentAttempt::STATUS_REQUIRES_ACTION,
                RepaymentAttempt::STATUS_PROCESSING
            ]);

        if ($this->option('fi')) {
            $query->where('financial_institution_id', $this->option('fi'));
        }

        $attempts = $query->get();
        $this->info("Syncing {$attempts->count()} repayment attempts...");

        $updated = 0;
        foreach ($attempts as $attempt) {
            try {
                $paymentIntent = $stripe->paymentIntents->retrieve($attempt->stripe_payment_intent_id);

                $newStatus = $this->mapStripeStatus($paymentIntent->status);
                if ($newStatus !== $attempt->status) {
                    $attempt->update(['status' => $newStatus]);
                    $updated++;

                    $this->info("Updated attempt {$attempt->id}: {$attempt->status} → {$newStatus}");
                }
            } catch (\Exception $e) {
                $this->error("Failed to sync attempt {$attempt->id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully updated {$updated} repayment attempts.");
    }

    private function mapStripeStatus(string $stripeStatus): string
    {
        return match ($stripeStatus) {
            'requires_payment_method', 'requires_action' => RepaymentAttempt::STATUS_REQUIRES_ACTION,
            'processing' => RepaymentAttempt::STATUS_PROCESSING,
            'succeeded' => RepaymentAttempt::STATUS_SUCCEEDED,
            'canceled' => RepaymentAttempt::STATUS_CANCELED,
            default => RepaymentAttempt::STATUS_FAILED
        };
    }
}
