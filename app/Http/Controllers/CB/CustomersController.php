<?php

namespace App\Http\Controllers\CB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomersController extends Controller
{

    // app/Http/Controllers/CB/CustomersController.php
    public function search(Request $request)
    {
        $payload = $request->validate([
            'q'    => 'required|string|max:200',  // e.g. email or name
            'limit' => 'sometimes|integer|min:1|max:20',
        ]);

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        // Stripe search syntax, typical email match:
        // https://docs.stripe.com/search#query-fields
        $query = "email:'{$payload['q']}' OR name:'{$payload['q']}'";

        $res = $stripe->customers->search([
            'query' => $query,
            'limit' => $payload['limit'] ?? 10,
        ]);

        $out = array_map(function ($c) {
            return [
                'id'    => $c->id,
                'email' => $c->email,
                'name'  => $c->name,
                'phone' => $c->phone,
            ];
        }, $res->data ?? []);

        return response()->json(['data' => $out]);
    }
}
