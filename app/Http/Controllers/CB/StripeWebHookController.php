<?php

namespace App\Http\Controllers\CB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Throwable;

class StripeWebHookController extends Controller
{
    public function handle(Request $request)
    {
        $sig = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($request->getContent(), $sig, $secret); // verify signature. :contentReference[oaicite:5]{index=5}
        } catch (Throwable $e) {
            return response()->json(['error' => 'INVALID_SIGNATURE'], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                // $pi = $event->data->object;
                // Read $pi->metadata (fi_id, loan_id, borrower_id)
                // Mark installment as paid, update balances, etc.
                break;

            case 'payment_intent.payment_failed':
                // Record failure, notify borrower/user
                break;

            case 'charge.refunded':
            case 'charge.dispute.created':
                // Handle refunds/disputes as your business requires
                break;
        }

        return response()->json(['received' => true]);
    }
}
