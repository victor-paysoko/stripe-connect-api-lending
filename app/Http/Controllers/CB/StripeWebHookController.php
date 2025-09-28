<?php

namespace App\Http\Controllers\CB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Stripe\WebhookSignature;
use App\Models\RepaymentAttempt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (\Exception $e) {
            Log::error('Webhook signature verification failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Check if we've processed this event already
        if ($this->eventProcessed($event->id)) {
            return response()->json(['status' => 'already_processed']);
        }

        Log::info('Stripe webhook received', ['type' => $event->type, 'id' => $event->id]);

        try {
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentIntentFailed($event->data->object);
                    break;

                case 'payment_intent.canceled':
                    $this->handlePaymentIntentCanceled($event->data->object);
                    break;
            }

            $this->markEventProcessed($event->id);
            return response()->json(['status' => 'success']);
        } catch (Throwable $e) {
            Log::error('Webhook processing failed', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        DB::transaction(function () use ($paymentIntent) {
            $attempt = RepaymentAttempt::where('stripe_payment_intent_id', $paymentIntent->id)->first();

            if (!$attempt) {
                Log::warning('PaymentIntent succeeded but no repayment attempt found', ['pi_id' => $paymentIntent->id]);
                return;
            }

            // Only process if not already succeeded (idempotency)
            if ($attempt->status === 'succeeded') {
                return;
            }

            $attempt->update([
                'status' => 'succeeded',
                'failure_reason' => null,
            ]);

            // Update your loan system - mark installment as paid
            $this->markInstallmentAsPaid(
                $attempt->loan_id,
                $attempt->installment_number,
                $attempt->amount_due
            );

            Log::info('Payment succeeded', [
                'repayment_attempt_id' => $attempt->id,
                'loan_id' => $attempt->loan_id,
                'amount' => $attempt->amount_due
            ]);
        });
    }

    private function handlePaymentIntentFailed($paymentIntent)
    {
        $attempt = RepaymentAttempt::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($attempt) {
            $attempt->update([
                'status' => 'failed',
                'failure_reason' => $paymentIntent->last_payment_error->message ?? 'Payment failed',
            ]);

            // Notify customer about failed payment
            $this->notifyPaymentFailure($attempt);
        }
    }

    private function handlePaymentIntentCanceled($paymentIntent)
    {
        $attempt = RepaymentAttempt::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($attempt) {
            $attempt->update(['status' => 'canceled']);
        }
    }

    private function eventProcessed(string $eventId): bool
    {
        return \App\Models\ProcessedWebhookEvent::where('event_id', $eventId)->exists();
    }

    private function markEventProcessed(string $eventId)
    {
        \App\Models\ProcessedWebhookEvent::create(['event_id' => $eventId]);
    }

    private function markInstallmentAsPaid(string $loanId, int $installmentNumber, int $amount)
    {
        // Your logic to update the loan system
        // This would interact with your FI's database
    }

    private function notifyPaymentFailure(RepaymentAttempt $attempt)
    {
        // Send email/SMS to customer about failed payment
        // Suggest updating payment method
    }
}
