<?php

namespace App\Http\Controllers\CB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BorrowersController extends Controller
{

    // app/Http/Controllers/CB/BorrowersController.php
    public function linkStripeCustomer(Request $request, string $fiId, string $borrowerId)
    {
        $data = $request->validate([
            'customer_id' => 'required|string|starts_with:cus_',
        ]);

        $borrower = Borrower::where('fi_id', $fiId)->where('id', $borrowerId)->firstOrFail();

        // (optional) verify the Customer exists in your Stripe acct
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        $customer = $stripe->customers->retrieve($data['customer_id'], []);
        if (!empty($customer->deleted)) {
            return response()->json(['error' => 'CUSTOMER_NOT_FOUND'], 404);
        }

        $borrower->stripe_customer_id = $customer->id;
        $borrower->save();

        return response()->json(['linked' => true, 'customer_id' => $customer->id]);
    }
}
