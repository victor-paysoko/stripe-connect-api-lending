<?php

namespace App\Http\Controllers\CB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\StripeClient;
use Throwable;
use App\Models\RepaymentAttempt;
use App\Models\Borrower;
use App\Models\FinancialInstitution;

class PaymentIntentsController extends Controller
{

    public function createOrConfirm(Request $request, string $fiId)
{
    // Use the same validation as your working endpoint
    $payload = $request->validate([
        'amount'              => 'required|integer|min:100',
        'currency'            => 'sometimes|string|size:3',
        'fi_account_id'       => 'required|string', // Keep this field
        'customer_id'         => 'nullable|string|starts_with:cus_',
        'borrower_email'      => 'sometimes|email',
        'borrower_name'       => 'sometimes|string|max:120',
        'borrower_phone'      => 'sometimes|string|max:40',
        'payment_method'      => 'nullable|string', // Keep 'payment_method' not 'payment_method_id'
        'platform_fee_amount' => 'nullable|integer|min:0',
        'loan_id'             => 'required|string',
        'borrower_id'         => 'required|string',
        'description'         => 'nullable|string|max:255',
        'confirm_now'         => 'sometimes|boolean', // Keep this field
        'return_url'          => 'sometimes|url',
    ]);

    // Set defaults
    $currency = $payload['currency'] ?? 'usd';
    $confirmNow = (bool)($payload['confirm_now'] ?? false);
    $paymentMethod = trim((string)($payload['payment_method'] ?? ''));

    $stripe = new StripeClient(config('services.stripe.secret'));
    $idempotencyKey = $request->header('Idempotency-Key') ?: 'repay_' . Str::uuid();

    // Idempotency check
    $existingAttempt = RepaymentAttempt::where('idempotency_key', $idempotencyKey)->first();
    if ($existingAttempt) {
        return $this->formatPaymentResponse($existingAttempt);
    }

    try {
        // Use fi_account_id from payload instead of looking it up
        $fiAccountId = $payload['fi_account_id'];

        // Resolve customer (your existing logic)
        $customerId = $this->resolveCustomerId($fiId, $payload);

        // Create repayment attempt
        $repaymentAttempt = RepaymentAttempt::create([
            'id' => Str::uuid(),
            'financial_institution_id' => $fiId,
            'loan_id' => $payload['loan_id'],
            'borrower_id' => $payload['borrower_id'],
            'installment_number' => $payload['installment_number'] ?? 1, // Add default
            'amount_due' => $payload['amount'],
            'currency' => $currency,
            'platform_fee_amount' => $payload['platform_fee_amount'] ?? $this->calculatePlatformFee($payload['amount']),
            'status' => 'pending',
            'idempotency_key' => $idempotencyKey,
            'metadata' => json_encode([
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ]),
        ]);

        // Build PI params (similar to your working version)
        $piParams = [
            'amount'   => $payload['amount'],
            'currency' => $currency,
            'automatic_payment_methods' => [
                'enabled'         => true,
                'allow_redirects' => ($confirmNow && empty($payload['return_url'])) ? 'never' : 'always',
            ],
            'transfer_data' => ['destination' => $fiAccountId],
            'on_behalf_of'  => $fiAccountId,
            'application_fee_amount' => $payload['platform_fee_amount'] ?? $this->calculatePlatformFee($payload['amount']),
            'description'  => $payload['description'] ?? "Loan repayment {$payload['loan_id']}",
            'metadata'     => [
                'repayment_attempt_id' => $repaymentAttempt->id,
                'fi_id'       => $fiId,
                'loan_id'     => $payload['loan_id'],
                'borrower_id' => $payload['borrower_id'],
                'purpose'     => 'loan_repayment',
            ],
        ];

        // Add customer if available
        if ($customerId !== '') {
            $piParams['customer'] = $customerId;
            if (!empty($payload['borrower_email'])) {
                $piParams['receipt_email'] = $payload['borrower_email'];
            }
        }

        // Add payment method if provided
        if (!empty($paymentMethod)) {
            $piParams['payment_method'] = $paymentMethod;
        }

        // Confirm immediately if requested
        if ($confirmNow) {
            $piParams['confirm'] = true;
            if (!empty($payload['return_url'])) {
                $piParams['return_url'] = $payload['return_url'];
            }
        }

        // Create PaymentIntent
        $paymentIntent = $stripe->paymentIntents->create($piParams, [
            'idempotency_key' => $idempotencyKey . '_pi'
        ]);

        // Update attempt
        $repaymentAttempt->update([
            'stripe_payment_intent_id' => $paymentIntent->id,
            'status' => $this->mapStripeStatusToInternal($paymentIntent->status)
        ]);

        $repaymentAttempt->refresh();

        // Return response similar to your working version
        if ($confirmNow) {
            return response()->json(array_merge(
                $paymentIntent->toArray(),
                ['repayment_attempt_id' => $repaymentAttempt->id]
            ), 201);
        }

        return response()->json([
            'payment_intent' => $paymentIntent->id,
            'client_secret'  => $paymentIntent->client_secret,
            'customer_id'    => $customerId,
            'repayment_attempt_id' => $repaymentAttempt->id,
        ], 201);

    } catch (Throwable $e) {
        if (isset($repaymentAttempt)) {
            $repaymentAttempt->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage()
            ]);
        }

        return response()->json([
            'error' => 'PAYMENT_PROCESSING_FAILED',
            'message' => $e->getMessage(),
            'repayment_attempt_id' => $repaymentAttempt->id ?? null,
        ], 422);
    }
}


    public function getStatus(string $repaymentAttemptId)
    {
        $attempt = RepaymentAttempt::find($repaymentAttemptId);

        if (!$attempt) {
            return response()->json([
                'error' => 'REPAYMENT_ATTEMPT_NOT_FOUND',
                'message' => 'The specified repayment attempt was not found'
            ], 404);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));
        $fiAccountId = $this->lookupStripeAccountId($attempt->financial_institution_id);

        try {
            $paymentIntent = null;
            if ($attempt->stripe_payment_intent_id) {
                // Retrieve from FI's Stripe account
                $paymentIntent = $stripe->paymentIntents->retrieve(
                    $attempt->stripe_payment_intent_id,
                    [],
                    ['stripe_account' => $fiAccountId]
                );

                // Sync status from Stripe
                $attempt->update([
                    'status' => $this->mapStripeStatusToInternal($paymentIntent->status)
                ]);
                $attempt->refresh();
            }

            return $this->formatPaymentResponse($attempt, $paymentIntent);
        } catch (Throwable $e) {
            return response()->json([
                'repayment_attempt_id' => $attempt->id,
                'status' => $attempt->status,
                'amount' => $attempt->amount_due,
                'currency' => $attempt->currency,
                'error' => 'Failed to sync with Stripe: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * POST /api/v1/repayments/{repaymentAttemptId}/actions
     */
    public function handleAction(Request $request, string $repaymentAttemptId)
    {
        $payload = $request->validate([
            'action' => 'required|string|in:retry,complete_3ds,cancel',
            'payment_method_id' => 'required_if:action,retry|nullable|string|starts_with:pm_',
        ]);

        $attempt = RepaymentAttempt::find($repaymentAttemptId);

        if (!$attempt) {
            return response()->json([
                'error' => 'REPAYMENT_ATTEMPT_NOT_FOUND',
                'message' => 'The specified repayment attempt was not found'
            ], 404);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));
        $fiAccountId = $this->lookupStripeAccountId($attempt->financial_institution_id);

        try {
            switch ($payload['action']) {
                case 'retry':
                    if (empty($payload['payment_method_id'])) {
                        return response()->json([
                            'error' => 'PAYMENT_METHOD_REQUIRED',
                            'message' => 'Payment method ID is required for retry action'
                        ], 422);
                    }

                    $confirmedIntent = $stripe->paymentIntents->confirm(
                        $attempt->stripe_payment_intent_id,
                        ['payment_method' => $payload['payment_method_id']],
                        ['stripe_account' => $fiAccountId]
                    );
                    $attempt->update([
                        'status' => $this->mapStripeStatusToInternal($confirmedIntent->status)
                    ]);
                    break;

                case 'complete_3ds':
                    $intent = $stripe->paymentIntents->retrieve(
                        $attempt->stripe_payment_intent_id,
                        [],
                        ['stripe_account' => $fiAccountId]
                    );
                    $attempt->update([
                        'status' => $this->mapStripeStatusToInternal($intent->status)
                    ]);
                    break;

                case 'cancel':
                    $cancelledIntent = $stripe->paymentIntents->cancel(
                        $attempt->stripe_payment_intent_id,
                        [],
                        ['stripe_account' => $fiAccountId]
                    );
                    $attempt->update(['status' => 'canceled']);
                    break;
            }

            $attempt->refresh();
            return $this->formatPaymentResponse($attempt);
        } catch (Throwable $e) {
            \Log::error('Payment action failed', [
                'action' => $payload['action'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'PAYMENT_ACTION_FAILED',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Enhanced Customer Resolution with proper linking
     */
    private function resolveCustomerId(string $fiId, array $payload): string
    {
        // First, try to find borrower in our database
        $borrower = Borrower::where('financial_institution_id', $fiId)
            ->where('external_borrower_id', $payload['borrower_id'])
            ->first();

        $stripe = new StripeClient(config('services.stripe.secret'));

        // If borrower exists and has Stripe customer ID, use it
        if ($borrower && $borrower->stripe_customer_id) {
            // Verify the customer still exists in Stripe
            try {
                $stripe->customers->retrieve($borrower->stripe_customer_id);
                return $borrower->stripe_customer_id;
            } catch (Throwable $e) {
                // Customer doesn't exist in Stripe, continue to create new one
                \Log::warning('Stripe customer not found, creating new', [
                    'customer_id' => $borrower->stripe_customer_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Try to find customer by email in Stripe
        if (!empty($payload['borrower_email'])) {
            try {
                $customers = $stripe->customers->search([
                    'query' => "email:'{$payload['borrower_email']}'",
                    'limit' => 1,
                ]);

                if (!empty($customers->data[0])) {
                    $customer = $customers->data[0];

                    // Save/update borrower record with this customer ID
                    if ($borrower) {
                        $borrower->update(['stripe_customer_id' => $customer->id]);
                    } else {
                        Borrower::create([
                            'id' => Str::uuid(),
                            'financial_institution_id' => $fiId,
                            'external_borrower_id' => $payload['borrower_id'],
                            'email' => $payload['borrower_email'],
                            'name' => $payload['borrower_name'] ?? null,
                            'phone' => $payload['borrower_phone'] ?? null,
                            'stripe_customer_id' => $customer->id,
                        ]);
                    }

                    return $customer->id;
                }
            } catch (Throwable $e) {
                \Log::warning('Customer search failed', ['error' => $e->getMessage()]);
            }
        }

        // Create new Stripe customer
        $customerData = [
            'email' => $payload['borrower_email'] ?? null,
            'name' => $payload['borrower_name'] ?? null,
            'phone' => $payload['borrower_phone'] ?? null,
            'metadata' => [
                'fi_id' => $fiId,
                'borrower_id' => $payload['borrower_id'],
                'loan_id' => $payload['loan_id'],
            ],
        ];

        $customer = $stripe->customers->create($customerData);

        // Create or update borrower record
        if ($borrower) {
            $borrower->update(['stripe_customer_id' => $customer->id]);
        } else {
            Borrower::create([
                'id' => Str::uuid(),
                'financial_institution_id' => $fiId,
                'external_borrower_id' => $payload['borrower_id'],
                'email' => $payload['borrower_email'] ?? null,
                'name' => $payload['borrower_name'] ?? null,
                'phone' => $payload['borrower_phone'] ?? null,
                'stripe_customer_id' => $customer->id,
            ]);
        }

        \Log::info('Created new Stripe customer', [
            'customer_id' => $customer->id,
            'fi_id' => $fiId,
            'borrower_id' => $payload['borrower_id']
        ]);

        return $customer->id;
    }

    private function formatPaymentResponse(RepaymentAttempt $attempt, $paymentIntent = null)
    {
        $response = [
            'repayment_attempt_id' => $attempt->id,
            'status' => $attempt->status,
            'amount' => $attempt->amount_due,
            'currency' => $attempt->currency,
            'loan_id' => $attempt->loan_id,
            'created_at' => $attempt->created_at->toISOString(),
        ];

        if ($paymentIntent) {
            $response['client_secret'] = $paymentIntent->client_secret;
            $response['requires_action'] = $paymentIntent->status === 'requires_action';
            $response['stripe_payment_intent_id'] = $paymentIntent->id;

            if ($paymentIntent->status === 'requires_action') {
                $response['next_action'] = $paymentIntent->next_action;
            }
        }

        if ($attempt->failure_reason) {
            $response['failure_reason'] = $attempt->failure_reason;
        }

        return response()->json($response);
    }

    private function mapStripeStatusToInternal(string $stripeStatus): string
    {
        return match ($stripeStatus) {
            'requires_payment_method', 'requires_action' => 'requires_action',
            'processing' => 'processing',
            'succeeded' => 'succeeded',
            'canceled' => 'canceled',
            default => 'failed'
        };
    }

    private function calculatePlatformFee(int $amount): int
    {
        return (int) round($amount * 0.01); // 1% platform fee
    }

    private function lookupStripeAccountId(string $fiId): ?string
    {
        return FinancialInstitution::where('id', $fiId)->value('stripe_account_id');
    }
}
