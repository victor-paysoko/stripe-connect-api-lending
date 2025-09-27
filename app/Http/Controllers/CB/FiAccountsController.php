<?php

namespace App\Http\Controllers\CB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Throwable;

/**
 * Expects you already store each FI with columns:
 * - id (your FI ID)
 * - stripe_account_id (acct_...)
 */
class FiAccountsController extends Controller
{
    public function health(string $fiId)
    {
        // TODO: load from your DB by $fiId
        $stripeAccountId = $this->lookupStripeAccountId($fiId);
        if (!$stripeAccountId) {
            return response()->json(['ok' => false, 'reason' => 'MISSING_STRIPE_ACCOUNT'], 404);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            $acct = $stripe->accounts->retrieve($stripeAccountId, []);
            return response()->json([
                'ok'              => (bool)($acct->charges_enabled ?? false),
                'charges_enabled' => (bool)($acct->charges_enabled ?? false),
                'payouts_enabled' => (bool)($acct->payouts_enabled ?? false),
                'capabilities'    => $acct->capabilities,
            ]);
        } catch (Throwable $e) {
            return response()->json(['ok' => false, 'reason' => 'STRIPE_ERROR', 'message' => $e->getMessage()], 422);
        }
    }

    private function lookupStripeAccountId(string $fiId): ?string
    {
        // Replace with your real repository/ORM lookup
        // return FI::where('id', $fiId)->value('stripe_account_id');
        return null;
    }
}
