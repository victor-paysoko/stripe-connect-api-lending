<?php

namespace App\Http\Controllers\CB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\StripeClient;
use Throwable;

/**
 * C → B repayments:
 * - Destination charge with transfer_data[destination] = acct_FI
 * - on_behalf_of = acct_FI (correct fees/branding & cross-border behavior)
 * - optional application_fee_amount (platform fee)
 */
class PaymentIntentsController extends Controller
{

    // POST /api/v1/fis/{fiId}/repayments/payment-intent
    public function create(Request $request, string $fiId)
    {
        $payload = $request->validate([
            'amount'              => 'required|integer|min:100',
            'currency'            => 'sometimes|string|size:3',
            'fi_account_id'       => 'required|string',
            'customer_id'         => 'nullable|string',
            'payment_method'      => 'nullable|string',
            'platform_fee_amount' => 'nullable|integer|min:0',
            'loan_id'             => 'required|string',
            'borrower_id'         => 'required|string',
            'description'         => 'nullable|string|max:255',
            'confirm_now'         => 'sometimes|boolean',
            'return_url'          => 'sometimes|url',
        ]);

        $currency   = strtolower($payload['currency'] ?? 'usd');
        $confirmNow = (bool)($payload['confirm_now'] ?? false);
        $pm         = trim((string)($payload['payment_method'] ?? ''));
        $returnUrl  = trim((string)($payload['return_url'] ?? ''));

        $params = [
            'amount'   => $payload['amount'],
            'currency' => $currency,

            'automatic_payment_methods' => [
                'enabled' => true,
                // if we're going to confirm now BUT caller didn't give a return_url,
                // disallow redirects so confirm doesn't require return_url.
                // Otherwise, leave redirects allowed and pass return_url below.
                'allow_redirects' => ($confirmNow && $returnUrl === '') ? 'never' : 'always',
            ],

            'transfer_data' => [
                'destination' => $payload['fi_account_id'],
            ],
            'on_behalf_of' => $payload['fi_account_id'],

            'application_fee_amount' => $payload['platform_fee_amount'] ?? 0,
            'description'            => $payload['description'] ?? "Loan repayment {$payload['loan_id']}",
            'metadata'               => [
                'fi_id'       => $fiId,
                'loan_id'     => $payload['loan_id'],
                'borrower_id' => $payload['borrower_id'],
                'purpose'     => 'loan_repayment',
            ],
        ];

        $customerId = trim((string)($payload['customer_id'] ?? ''));
        if ($customerId !== '') {
            $params['customer'] = $customerId;
        }
        if ($pm !== '') {
            $params['payment_method'] = $pm;
        }

        // If confirming in this call:
        if ($confirmNow) {
            $params['confirm'] = true;

            // If redirects allowed, Stripe requires a return_url for confirm
            if ($returnUrl !== '') {
                $params['return_url'] = $returnUrl;
            }

        }

        $stripe    = new StripeClient(config('services.stripe.secret'));
        $idempoKey = $request->header('Idempotency-Key') ?: 'pi_' . Str::uuid();

        try {
            $pi = $stripe->paymentIntents->create($params, [
                'idempotency_key' => $idempoKey,
            ]);

            // If we confirmed, return the full PI (status, charges, next_action)
            if ($confirmNow) {
                return response()->json($pi, 201);
            }

            // Create-only: return id + client_secret for Payment Element
            return response()->json([
                'payment_intent' => $pi->id,
                'client_secret'  => $pi->client_secret,
            ], 201);
        } catch (Throwable $e) {
            return response()->json([
                'error'   => 'PAYMENT_INTENT_CREATE_FAILED',
                'message' => $e->getMessage(),
            ], 422);
        }
    }


    public function confirm(Request $request, string $piId)
    {
        $payload = $request->validate([
            'payment_method' => 'nullable|string',
            'return_url'     => 'sometimes|url',
        ]);



        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            // First retrieve the PaymentIntent to get the connected account
            $pi = $stripe->paymentIntents->retrieve($piId);

            // Get the connected account ID from the transfer_data or on_behalf_of
            $connectedAccountId = $pi->on_behalf_of ?? $pi->transfer_data->destination ?? null;

            $confirmParams = [];

            if (!empty($payload['payment_method'])) {
                $confirmParams['payment_method'] = $payload['payment_method'];
            }

            if (!empty($payload['return_url'])) {
                $confirmParams['return_url'] = $payload['return_url'];
            }

            // Options array for the API call
            $options = [];
            if ($connectedAccountId) {
                // Use the connected account header for proper fee handling
                $options['stripe_account'] = $connectedAccountId;
            }

            $confirmedPi = $stripe->paymentIntents->confirm($piId, $confirmParams, $options);

            return response()->json([
                'status' => 'success',
                'payment_intent' => $confirmedPi,
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'error'   => 'PAYMENT_INTENT_CONFIRM_FAILED',
                'message' => $e->getMessage(),
                'details' => [
                    'payment_intent_id' => $piId,
                    'error_type' => get_class($e),
                ]
            ], 422);
        }
    }


    public function cancel(string $piId)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            // First retrieve to get connected account info
            $pi = $stripe->paymentIntents->retrieve($piId);
            $connectedAccountId = $pi->on_behalf_of ?? $pi->transfer_data->destination ?? null;

            $options = [];
            if ($connectedAccountId) {
                $options['stripe_account'] = $connectedAccountId;
            }

            $cancelledPi = $stripe->paymentIntents->cancel($piId, [], $options);

            return response()->json([
                'status' => 'success',
                'payment_intent' => $cancelledPi,
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'error'   => 'PAYMENT_INTENT_CANCEL_FAILED',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function retrieve(string $piId)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));
        try {
            $pi = $stripe->paymentIntents->retrieve($piId);
            return response()->json($pi);
        } catch (Throwable $e) {
            return response()->json(['error' => 'PAYMENT_INTENT_RETRIEVE_FAILED', 'message' => $e->getMessage()], 404);
        }
    }
}
