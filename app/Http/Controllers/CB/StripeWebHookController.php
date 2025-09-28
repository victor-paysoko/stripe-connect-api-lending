<?php

namespace App\Http\Controllers\CB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Throwable;
use Illuminate\Support\Facades\DB;



class StripeWebHookController extends Controller
{

    public function handle(Request $request)
    {
        $sig    = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($request->getContent(), $sig, $secret);
        } catch (Throwable $e) {
            // signature fail
            return response()->json(['error' => 'INVALID_SIGNATURE'], 400);
        }

        // Idempotency: skip if we processed this event_id already
        $already = DB::table('stripe_webhook_events')->where('event_id', $event->id)->exists();
        if ($already) {
            return response()->json(['ok' => true, 'dedup' => true]);
        }

        try {
            DB::beginTransaction();

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $pi = $event->data->object; // \Stripe\PaymentIntent

                    $loanId     = (string)($pi->metadata->loan_id ?? '');
                    $borrowerId = (string)($pi->metadata->borrower_id ?? '');
                    $fiAcct     = (string)($pi->transfer_data->destination ?? $pi->on_behalf_of ?? '');
                    $amount     = (int)$pi->amount; // minor units
                    $currency   = (string)$pi->currency;
                    $chargeId   = (string)($pi->latest_charge ?? '');

                    // Find the matching pending installment
                    $updated = DB::table('loan_installments')
                        ->where('loan_id', $loanId)
                        ->where('borrower_id', $borrowerId)
                        ->where('fi_account_id', $fiAcct)
                        ->where('currency', $currency)
                        ->where('status', 'pending')
                        // (Optional) also match amount to the cent to be strict:
                        ->where('amount_cents', $amount)
                        ->update([
                            'status'                    => 'paid',
                            'paid_at'                   => now(),
                            'stripe_payment_intent_id'  => $pi->id,
                            'stripe_charge_id'          => $chargeId ?: DB::raw('stripe_charge_id'),
                            'updated_at'                => now(),
                        ]);

                    // If nothing matched, you might want to insert an audit row or log
                    if ($updated === 0) {
                        // fallback: mark by PI id if you created the row earlier with this id
                        DB::table('loan_installments')
                            ->whereNull('stripe_payment_intent_id')
                            ->where('loan_id', $loanId)
                            ->where('borrower_id', $borrowerId)
                            ->limit(1)
                            ->update([
                                'status'                   => 'paid',
                                'paid_at'                  => now(),
                                'stripe_payment_intent_id' => $pi->id,
                                'stripe_charge_id'         => $chargeId ?: null,
                                'updated_at'               => now(),
                            ]);
                    }

                    break;

                case 'payment_intent.payment_failed':
                    $pi = $event->data->object;
                    DB::table('loan_installments')
                        ->where('loan_id', (string)$pi->metadata->loan_id)
                        ->where('borrower_id', (string)$pi->metadata->borrower_id)
                        ->where('status', 'pending')
                        ->update([
                            'status'     => 'failed',
                            'updated_at' => now(),
                        ]);
                    break;

                    // add other events as needed (refunds/disputes)
            }

            // Record event id so we never process twice
            DB::table('stripe_webhook_events')->insert(['event_id' => $event->id]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            // Log & return 200 so Stripe doesn't retry forever, or return 500 to have Stripe retry—your call.
            \Log::error('Stripe webhook error: ' . $e->getMessage(), ['eventId' => $event->id]);
            return response()->json(['error' => 'WEBHOOK_PROCESSING_FAILED'], 200);
        }

        return response()->json(['received' => true], 200);
    }
}
