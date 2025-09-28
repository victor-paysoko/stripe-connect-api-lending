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
            // either provide an existing customer_id...
            'customer_id'         => 'nullable|string|starts_with:cus_',
            // ...or provide borrower contact so we can find/create:
            'borrower_email'      => 'sometimes|email',
            'borrower_name'       => 'sometimes|string|max:120',
            'borrower_phone'      => 'sometimes|string|max:40',

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

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        // --- Ensure/resolve a Stripe Customer ---
        $customerId = trim((string)($payload['customer_id'] ?? ''));

        // 1) If borrower already linked in your DB, prefer that
        $borrower = Borrower::where('fi_id', $fiId)->where('id', $payload['borrower_id'])->first();
        if ($borrower && !empty($borrower->stripe_customer_id)) {
            $customerId = $borrower->stripe_customer_id;
        }

        // 2) If still empty, try to find by email
        if ($customerId === '' && !empty($payload['borrower_email'])) {
            $res = $stripe->customers->search([
                'query' => "email:'{$payload['borrower_email']}'",
                'limit' => 1,
            ]);
            if (!empty($res->data[0]?->id)) {
                $customerId = $res->data[0]->id;
            }
        }

        // 3) If still empty, create a new Customer (and persist it)
        if ($customerId === '') {
            // You can also add metadata like fiId/borrowerId here
            $created = $stripe->customers->create([
                'email' => $payload['borrower_email'] ?? null,
                'name'  => $payload['borrower_name']  ?? null,
                'phone' => $payload['borrower_phone'] ?? null,
                'metadata' => [
                    'fi_id'       => $fiId,
                    'borrower_id' => $payload['borrower_id'],
                ],
            ]);
            $customerId = $created->id;

            // Save back to your DB for reuse next time
            if ($borrower) {
                $borrower->stripe_customer_id = $customerId;
                $borrower->save();
            }
        }

        // --- Build PI params ---
        $params = [
            'amount'   => $payload['amount'],
            'currency' => $currency,
            'automatic_payment_methods' => [
                'enabled'         => true,
                'allow_redirects' => ($confirmNow && $returnUrl === '') ? 'never' : 'always',
            ],
            'transfer_data' => ['destination' => $payload['fi_account_id']],
            'on_behalf_of'  => $payload['fi_account_id'],
            'application_fee_amount' => $payload['platform_fee_amount'] ?? 0,
            'description'  => $payload['description'] ?? "Loan repayment {$payload['loan_id']}",
            'metadata'     => [
                'fi_id'       => $fiId,
                'loan_id'     => $payload['loan_id'],
                'borrower_id' => $payload['borrower_id'],
                'purpose'     => 'loan_repayment',
            ],
        ];

        // attach the resolved customer
        if ($customerId !== '') {
            $params['customer'] = $customerId;
            // nice-to-have: emails a receipt
            if (!empty($payload['borrower_email'])) {
                $params['receipt_email'] = $payload['borrower_email'];
            }
        }

        if ($pm !== '') {
            $params['payment_method'] = $pm;
        }

        if ($confirmNow) {
            $params['confirm'] = true;
            if ($returnUrl !== '') {
                $params['return_url'] = $returnUrl;
            }
        }

        $idempoKey = $request->header('Idempotency-Key') ?: 'pi_' . \Illuminate\Support\Str::uuid();

        try {
            $pi = $stripe->paymentIntents->create($params, ['idempotency_key' => $idempoKey]);

            // If we confirmed, return the full PI (status, charges, next_action)
            if ($confirmNow) {
                return response()->json($pi, 201);
            }
            // else return id+client_secret
            return response()->json([
                'payment_intent' => $pi->id,
                'client_secret'  => $pi->client_secret,
                'customer_id'    => $customerId, // helpful to surface back to UI
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'PAYMENT_INTENT_CREATE_FAILED',
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
