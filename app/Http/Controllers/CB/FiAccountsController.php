<?php

namespace App\Http\Controllers\CB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Throwable;
use Illuminate\Support\Facades\Log;

/**
 * Expects you already store each FI with columns:
 * - id (your FI ID)
 * - stripe_account_id (acct_...)
 */
class FiAccountsController extends Controller
{
    public function health(string $fiId)
    {

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

    // public function listConnected(Request $request)
    // {
    //     // 1) validate & capture inputs
    //     $payload = $request->validate([
    //         'q'              => 'sometimes|string|max:100',
    //         'only_active'    => 'sometimes|boolean',
    //         'limit'          => 'sometimes|integer|min:1|max:100',
    //         'starting_after' => 'sometimes|string',
    //         'debug'          => 'sometimes|boolean',   // ?debug=1 will echo some raw fields
    //     ]);

    //     // 2) log request context (mask secrets)
    //     $maskedKey = substr((string)config('services.stripe.secret'), 0, 7) . '…';
    //     Log::info('[CA:list] incoming', [
    //         'ip'        => $request->ip(),
    //         'payload'   => $payload,
    //         'stripe_sk' => $maskedKey, // masked!
    //         'headers'   => [
    //             'User-Agent' => $request->header('User-Agent'),
    //             'X-Request-Id' => $request->header('X-Request-Id'),
    //         ],
    //     ]);

    //     $stripe = new StripeClient(config('services.stripe.secret'));

    //     $params = [
    //         'limit' => $payload['limit'] ?? 25,
    //     ];
    //     if (!empty($payload['starting_after'])) {
    //         $params['starting_after'] = $payload['starting_after'];
    //     }

    //     Log::info('[CA:list] list params', $params);

    //     try {
    //         // 3) call Stripe
    //         $accounts = $stripe->accounts->all($params);

    //         Log::info('[CA:list] stripe response summary', [
    //             'object'     => $accounts->object ?? null,
    //             'count'      => is_array($accounts->data ?? null) ? count($accounts->data) : null,
    //             'has_more'   => (bool)($accounts->has_more ?? false),
    //             'first_id'   => $accounts->data[0]->id ?? null,
    //             'last_id'    => end($accounts->data)->id ?? null,
    //         ]);

    //         // 4) filter / map for UI
    //         $q          = strtolower($payload['q'] ?? '');
    //         $onlyActive = (bool)($payload['only_active'] ?? false);

    //         $data = [];
    //         foreach ($accounts->data as $a) {
    //             if ($onlyActive && !$a->charges_enabled) continue;

    //             $displayName = $a->business_profile->name
    //                 ?? ($a->settings->dashboard->display_name ?? null)
    //                 ?? $a->email
    //                 ?? $a->id;

    //             if ($q !== '') {
    //                 $hay = strtolower(($displayName ?? '') . ' ' . ($a->email ?? '') . ' ' . ($a->id ?? ''));
    //                 if (mb_strpos($hay, $q) === false) continue;
    //             }

    //             $data[] = [
    //                 'id'               => $a->id,
    //                 'display_name'     => $displayName,
    //                 'email'            => $a->email,
    //                 'country'          => $a->country,
    //                 'default_currency' => $a->default_currency,
    //                 'charges_enabled'  => (bool)$a->charges_enabled,
    //                 'payouts_enabled'  => (bool)$a->payouts_enabled,
    //                 'created'          => $a->created,
    //             ];
    //         }

    //         $last = end($accounts->data) ?: null;

    //         // 5) if empty, log that clearly
    //         if (empty($data)) {
    //             Log::warning('[CA:list] no accounts after filter', [
    //                 'only_active' => $onlyActive,
    //                 'q'           => $q,
    //                 'raw_count'   => is_array($accounts->data ?? null) ? count($accounts->data) : null,
    //             ]);
    //         }

    //         // 6) optionally include a debug snapshot in the response
    //         $resp = [
    //             'data'        => array_values($data),
    //             'has_more'    => (bool)$accounts->has_more,
    //             'next_cursor' => $last?->id,
    //         ];

    //         if (!empty($payload['debug'])) {
    //             $resp['_debug'] = [
    //                 'raw_count'   => is_array($accounts->data ?? null) ? count($accounts->data) : null,
    //                 'first_raw'   => $accounts->data[0] ?? null,
    //                 'request_id'  => $accounts->getLastResponse()->headers['request-id'] ?? null,
    //                 'stripe_host' => $accounts->getLastResponse()->headers['stripe-maybe'] ?? null,
    //             ];
    //         }

    //         return response()->json($resp);
    //     } catch (Throwable $e) {
    //         Log::error('[CA:list] error', [
    //             'msg'   => $e->getMessage(),
    //             'code'  => method_exists($e, 'getCode') ? $e->getCode() : null,
    //             'trace' => substr($e->getTraceAsString(), 0, 2000), // trim
    //         ]);

    //         return response()->json([
    //             'error'   => 'CONNECTED_ACCOUNTS_LIST_FAILED',
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    // public function listConnected(Request $request)
    // {
    //     $payload = $request->validate([
    //         'q'              => 'sometimes|string|max:100',
    //         'only_active'    => 'sometimes|boolean',
    //         'limit'          => 'sometimes|integer|min:1|max:100',
    //         'starting_after' => 'sometimes|string',
    //         'debug'          => 'sometimes|boolean',
    //         'raw'            => 'sometimes|boolean',   // NEW: ?raw=1 returns Stripe's raw list
    //     ]);

    //     $maskedKey = substr((string)config('services.stripe.secret'), 0, 7) . '…';
    //     \Log::info('[CA:list] incoming', [
    //         'ip'        => $request->ip(),
    //         'payload'   => $payload,
    //         'stripe_sk' => $maskedKey,
    //         'headers'   => [
    //             'User-Agent' => $request->header('User-Agent'),
    //             'X-Request-Id' => $request->header('X-Request-Id'),
    //         ],
    //     ]);

    //     $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

    //     $params = ['limit' => $payload['limit'] ?? 25];
    //     if (!empty($payload['starting_after'])) {
    //         $params['starting_after'] = $payload['starting_after'];
    //     }
    //     \Log::info('[CA:list] list params', $params);

    //     try {
    //         $accounts = $stripe->accounts->all($params);

    //         \Log::info('[CA:list] stripe response summary', [
    //             'object'   => $accounts->object ?? null,
    //             'count'    => is_array($accounts->data ?? null) ? count($accounts->data) : null,
    //             'has_more' => (bool)($accounts->has_more ?? false),
    //             'first_id' => $accounts->data[0]->id ?? null,
    //             'last_id'  => end($accounts->data)->id ?? null,
    //         ]);

    //         // If caller wants raw Stripe payload (for debugging), short-circuit here.
    //         if (!empty($payload['raw'])) {
    //             return response()->json($accounts, 200);
    //         }

    //         $q          = strtolower($payload['q'] ?? '');
    //         $onlyActive = (bool)($payload['only_active'] ?? false);

    //         $data = [];
    //         foreach ($accounts->data as $a) {
    //             if ($onlyActive && !$a->charges_enabled) continue;

    //             // Use nullsafe operators to avoid notices if nested objects are null
    //             $displayName = $a->business_profile?->name
    //                 ?? $a->settings?->dashboard?->display_name
    //                 ?? $a->email
    //                 ?? $a->id;

    //             if ($q !== '') {
    //                 $hay = strtolower(($displayName ?? '') . ' ' . ($a->email ?? '') . ' ' . ($a->id ?? ''));
    //                 if (mb_strpos($hay, $q) === false) continue;
    //             }

    //             $data[] = [
    //                 'id'               => $a->id,
    //                 'display_name'     => $displayName,
    //                 'email'            => $a->email,
    //                 'country'          => $a->country,
    //                 'default_currency' => $a->default_currency,
    //                 'charges_enabled'  => (bool)$a->charges_enabled,
    //                 'payouts_enabled'  => (bool)$a->payouts_enabled,
    //                 'created'          => $a->created,
    //             ];
    //         }

    //         \Log::info('[CA:list] mapped count', ['mapped' => count($data)]);

    //         // Add one of these (or all) to see the contents:
    //         \Log::info('[CA:list] mapped payload (first 12)', ['data' => $data]);
    //         \Log::info('[CA:list] mapped pretty: ' . json_encode($data, JSON_PRETTY_PRINT));

    //         $summary = array_map(fn($x) => [
    //             'id' => $x['id'],
    //             'name' => $x['display_name'],
    //             'enabled' => $x['charges_enabled'],
    //         ], $data);
    //         \Log::info('[CA:list] mapped summary', ['accounts' => $summary]);


    //         $last = end($accounts->data) ?: null;

    //         if (empty($data)) {
    //             \Log::warning('[CA:list] no accounts after filter', [
    //                 'only_active' => $onlyActive,
    //                 'q'           => $q,
    //                 'raw_count'   => is_array($accounts->data ?? null) ? count($accounts->data) : null,
    //             ]);
    //         }

    //         $resp = [
    //             'data'        => array_values($data),
    //             'has_more'    => (bool)$accounts->has_more,
    //             'next_cursor' => $last?->id,
    //         ];

    //         if (!empty($payload['debug'])) {
    //             $resp['_debug'] = [
    //                 'raw_count'  => is_array($accounts->data ?? null) ? count($accounts->data) : null,
    //                 'first_raw'  => $accounts->data[0] ?? null,
    //                 'request_id' => $accounts->getLastResponse()->headers['request-id'] ?? null,
    //             ];
    //         }

    //         return response()->json($resp, 200);
    //     } catch (\Throwable $e) {
    //         \Log::error('[CA:list] error', [
    //             'msg'   => $e->getMessage(),
    //             'code'  => method_exists($e, 'getCode') ? $e->getCode() : null,
    //             'trace' => substr($e->getTraceAsString(), 0, 2000),
    //         ]);

    //         return response()->json([
    //             'error'   => 'CONNECTED_ACCOUNTS_LIST_FAILED',
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function listConnected(Request $request)
    {
        $payload = $request->validate([
            'q'              => 'sometimes|string|max:100',
            'only_active'    => 'sometimes|boolean',
            'limit'          => 'sometimes|integer|min:1|max:100',
            'starting_after' => 'sometimes|string',
            'debug'          => 'sometimes|boolean',
            'raw'            => 'sometimes|boolean',   // ?raw=1 returns Stripe raw list
        ]);

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        $params = ['limit' => $payload['limit'] ?? 25];
        if (!empty($payload['starting_after'])) {
            $params['starting_after'] = $payload['starting_after'];
        }

        try {
            $accounts = $stripe->accounts->all($params);

            // 1) Raw Stripe payload (for quick comparison)
            if ($request->boolean('raw')) {
                \Log::info('[CA:list] returning RAW', [
                    'raw_count' => is_array($accounts->data ?? null) ? count($accounts->data) : 0,
                ]);

                return response()
                    ->json($accounts, 200, [
                        'Content-Type'  => 'application/json; charset=utf-8',
                        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                        'Pragma'        => 'no-cache',
                    ])
                    ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            // 2) Map for UI
            $q          = strtolower($payload['q'] ?? '');
            $onlyActive = (bool)($payload['only_active'] ?? false);

            $data = [];
            foreach ($accounts->data as $a) {
                if ($onlyActive && !$a->charges_enabled) continue;

                $displayName = $a->business_profile?->name
                    ?? $a->settings?->dashboard?->display_name
                    ?? $a->email
                    ?? $a->id;

                if ($q !== '') {
                    $hay = strtolower(($displayName ?? '') . ' ' . ($a->email ?? '') . ' ' . ($a->id ?? ''));
                    if (mb_strpos($hay, $q) === false) continue;
                }

                $data[] = [
                    'id'               => $a->id,
                    'display_name'     => $displayName,
                    'email'            => $a->email,
                    'country'          => $a->country,
                    'default_currency' => $a->default_currency,
                    'charges_enabled'  => (bool)$a->charges_enabled,
                    'payouts_enabled'  => (bool)$a->payouts_enabled,
                    'created'          => $a->created,
                ];
            }

            $last = end($accounts->data) ?: null;

            $resp = [
                'count'       => count($data),
                'data'        => array_values($data),
                'has_more'    => (bool)$accounts->has_more,
                'next_cursor' => $last?->id,
            ];

            if ($request->boolean('debug')) {
                $resp['_debug'] = [
                    'raw_count'  => is_array($accounts->data ?? null) ? count($accounts->data) : 0,
                    'request_id' => $accounts->getLastResponse()->headers['request-id'] ?? null,
                ];
            }

            // 🔎 LOG the exact JSON we are returning
            \Log::info('[CA:list] returning RESP', [
                'resp_preview' => substr(json_encode($resp), 0, 1000),  // first 1k chars
                'count'        => $resp['count'],
            ]);

            // 🚚 Force a plain JSON response (no compression assumptions)
            return response()
                ->json($resp, 200, [
                    'Content-Type'  => 'application/json; charset=utf-8',
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                    'Pragma'        => 'no-cache',
                ])
                ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            \Log::error('[CA:list] error', [
                'msg' => $e->getMessage(),
            ]);
            return response()->json([
                'error'   => 'CONNECTED_ACCOUNTS_LIST_FAILED',
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    // GET /api/v1/stripe/connected-accounts/{accountId}
    public function getConnected(string $accountId)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            $a = $stripe->accounts->retrieve($accountId, []);
            $displayName = $a->business_profile->name
                ?? ($a->settings->dashboard->display_name ?? null)
                ?? $a->email
                ?? $a->id;

            return response()->json([
                'id'               => $a->id,
                'display_name'     => $displayName,
                'email'            => $a->email,
                'country'          => $a->country,
                'default_currency' => $a->default_currency,
                'charges_enabled'  => $a->charges_enabled,
                'payouts_enabled'  => $a->payouts_enabled,
                'capabilities'     => $a->capabilities,
                'created'          => $a->created,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'error'   => 'CONNECTED_ACCOUNT_FETCH_FAILED',
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    private function lookupStripeAccountId(string $fiId): ?string
    {

        return null;
    }


    // function all() {
    //   try {
    //    return view('')
    //   } catch (\Exception $th) {
    //     //throw $th;
    //   }
    // }
}
