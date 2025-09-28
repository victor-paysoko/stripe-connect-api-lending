<?php

namespace App\Http\Controllers\CB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use App\Models\Borrower;

class CustomersController extends Controller
{
    /**
     * GET /api/v1/fis/{fiId}/customers/{borrowerId}/payment-methods
     */
    public function getPaymentMethods(string $fiId, string $borrowerId)
    {
        $borrower = Borrower::where('fi_id', $fiId)
            ->where('id', $borrowerId)
            ->firstOrFail();

        if (!$borrower->stripe_customer_id) {
            return response()->json(['payment_methods' => []]);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            $paymentMethods = $stripe->paymentMethods->all([
                'customer' => $borrower->stripe_customer_id,
                'type' => 'card',
            ]);

            $formattedMethods = collect($paymentMethods->data)->map(function ($pm) {
                return [
                    'id' => $pm->id,
                    'type' => $pm->type,
                    'card' => [
                        'brand' => $pm->card->brand,
                        'last4' => $pm->card->last4,
                        'exp_month' => $pm->card->exp_month,
                        'exp_year' => $pm->card->exp_year,
                    ],
                    'is_default' => false, // You might track this separately
                ];
            });

            return response()->json(['payment_methods' => $formattedMethods]);
        } catch (Throwable $e) {
            return response()->json([
                'error' => 'FAILED_TO_FETCH_PAYMENT_METHODS',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * POST /api/v1/fis/{fiId}/customers/{borrowerId}/setup-intent
     * For securely saving payment methods without immediate payment
     */
    public function createSetupIntent(string $fiId, string $borrowerId)
    {
        $borrower = Borrower::where('fi_id', $fiId)
            ->where('id', $borrowerId)
            ->firstOrFail();

        // Ensure customer exists
        $customerId = $this->ensureCustomerExists($borrower);

        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            $setupIntent = $stripe->setupIntents->create([
                'customer' => $customerId,
                'payment_method_types' => ['card'],
                'metadata' => [
                    'fi_id' => $fiId,
                    'borrower_id' => $borrowerId,
                ],
            ]);

            return response()->json([
                'client_secret' => $setupIntent->client_secret,
                'setup_intent_id' => $setupIntent->id,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'error' => 'SETUP_INTENT_CREATE_FAILED',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    private function ensureCustomerExists(Borrower $borrower): string
    {
        if ($borrower->stripe_customer_id) {
            return $borrower->stripe_customer_id;
        }

        $stripe = new StripeClient(config('services.stripe.secret'));
        $customer = $stripe->customers->create([
            'email' => $borrower->email,
            'name' => $borrower->name,
            'metadata' => [
                'fi_id' => $borrower->fi_id,
                'borrower_id' => $borrower->id,
            ],
        ]);

        $borrower->update(['stripe_customer_id' => $customer->id]);
        return $customer->id;
    }
}
