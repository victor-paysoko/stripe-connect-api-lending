<?php

namespace App\Http\Controllers\CB;

use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
    public function show()
    {
        return response()->json([
            'publishableKey'  => config('services.stripe.publishable'),
            'defaultCurrency' => 'usd',
            'paymentElement'  => true,
        ]);
    }
}
