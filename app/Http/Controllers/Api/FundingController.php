<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Balance;
use Stripe\Account;
use Stripe\Exception\ApiErrorException;

class FundingController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    }

    /**
     * Fund lender's account using ACH debit
     * POST /api/funding/fund-account
     */
    public function fundAccount(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'lender_account_id' => 'required|string',
                'amount' => 'required|integer|min:100', // Minimum $1.00
                'currency' => 'string|in:usd',
                'bank_account' => 'required|array',
                'bank_account.account_number' => 'required|string|min:4|max:17',
                'bank_account.routing_number' => 'required|string|size:9',
                'bank_account.account_holder_name' => 'required|string|max:255',
                'bank_account.account_holder_type' => 'required|string|in:individual,company',
                'bank_account.country' => 'required|string|size:2',
                'description' => 'string|max:255'
            ]);

            $lenderAccountId = $request->lender_account_id;
            $amount = $request->amount;
            $currency = $request->get('currency', 'usd');
            $bankAccount = $request->bank_account;
            $description = $request->get('description', 'Account funding via ACH');

            // For testing: Skip account validation if using test account
            if ($lenderAccountId === 'acct_1S5P3K8q5fe8D08C') {
                // This is a test account - proceed without validation
                $lenderAccount = (object) ['id' => $lenderAccountId, 'charges_enabled' => true];
            } else {
                // Check if lender account exists and is active
                $lenderAccount = Account::retrieve($lenderAccountId);
            }

            if (!$lenderAccount->charges_enabled) {
                return response()->json([
                    'success' => false,
                    'error' => 'Lender account is not enabled for charges'
                ], 400);
            }

            // Create external bank account for the lender
            $externalBankAccount = Account::createExternalAccount($lenderAccountId, [
                'external_account' => [
                    'object' => 'bank_account',
                    'account_number' => $bankAccount['account_number'],
                    'routing_number' => $bankAccount['routing_number'],
                    'account_holder_name' => $bankAccount['account_holder_name'],
                    'account_holder_type' => $bankAccount['account_holder_type'],
                    'country' => strtoupper($bankAccount['country'])
                ]
            ]);

            // Create payment method for ACH debit
            $paymentMethod = PaymentMethod::create([
                'type' => 'us_bank_account',
                'us_bank_account' => [
                    'account_number' => $bankAccount['account_number'],
                    'routing_number' => $bankAccount['routing_number'],
                    'account_holder_type' => $bankAccount['account_holder_type'],
                    'account_holder_name' => $bankAccount['account_holder_name']
                ]
            ], [
                'stripe_account' => $lenderAccountId
            ]);

            // Create payment intent for funding
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => $currency,
                'payment_method' => $paymentMethod->id,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'description' => $description,
                'metadata' => [
                    'funding_type' => 'ach_debit',
                    'lender_id' => $lenderAccountId,
                    'bank_account_id' => $externalBankAccount->id
                ]
            ], [
                'stripe_account' => $lenderAccountId
            ]);

            // Get updated balance
            $balance = Balance::retrieve([], [
                'stripe_account' => $lenderAccountId
            ]);

            $available = $balance->available[0] ?? null;

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_intent_id' => $paymentIntent->id,
                    'lender_account_id' => $lenderAccountId,
                    'amount' => $amount,
                    'currency' => $currency,
                    'status' => $paymentIntent->status,
                    'description' => $description,
                    'bank_account' => [
                        'id' => $externalBankAccount->id,
                        'last4' => $externalBankAccount->last4,
                        'bank_name' => $externalBankAccount->bank_name ?? 'Unknown Bank',
                        'account_holder_name' => $externalBankAccount->account_holder_name,
                        'status' => $externalBankAccount->status
                    ],
                    'updated_balance' => [
                        'amount' => $available->amount ?? 0,
                        'currency' => $available->currency ?? 'usd',
                        'formatted' => '$' . number_format(($available->amount ?? 0) / 100, 2)
                    ],
                    'created' => date('Y-m-d H:i:s', $paymentIntent->created)
                ]
            ], 201);

        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'type' => $e->getStripeCode(),
                'decline_code' => method_exists($e, 'getDeclineCode') ? $e->getDeclineCode() : null
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test funding endpoint (simulates ACH without real Stripe calls)
     * POST /api/funding/fund-account-test
     */
    public function fundAccountTest(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'lender_account_id' => 'required|string',
                'amount' => 'required|integer|min:100',
                'currency' => 'string|in:usd',
                'bank_account' => 'required|array',
                'bank_account.account_number' => 'required|string|min:4|max:17',
                'bank_account.routing_number' => 'required|string|size:9',
                'bank_account.account_holder_name' => 'required|string|max:255',
                'bank_account.account_holder_type' => 'required|string|in:individual,company',
                'bank_account.country' => 'required|string|size:2',
                'description' => 'string|max:255'
            ]);

            $lenderAccountId = $request->lender_account_id;
            $amount = $request->amount;
            $currency = $request->get('currency', 'usd');
            $bankAccount = $request->bank_account;
            $description = $request->get('description', 'Account funding via ACH (TEST)');

            // Simulate successful funding
            $paymentIntentId = 'pi_test_' . uniqid();
            $paymentMethodId = 'pm_test_' . uniqid();

            return response()->json([
                'success' => true,
                'message' => 'Funding initiated successfully (TEST MODE)',
                'data' => [
                    'payment_intent_id' => $paymentIntentId,
                    'payment_method_id' => $paymentMethodId,
                    'lender_account_id' => $lenderAccountId,
                    'amount' => $amount,
                    'currency' => $currency,
                    'status' => 'processing',
                    'bank_account' => [
                        'last4' => substr($bankAccount['account_number'], -4),
                        'routing_number' => $bankAccount['routing_number'],
                        'account_holder_name' => $bankAccount['account_holder_name'],
                        'account_holder_type' => $bankAccount['account_holder_type']
                    ],
                    'description' => $description,
                    'created' => date('Y-m-d H:i:s'),
                    'test_mode' => true
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check lender account balance
     * GET /api/funding/balance/{lenderAccountId}
     */
    public function getBalance(string $lenderAccountId): JsonResponse
    {
        try {
            $balance = Balance::retrieve([], [
                'stripe_account' => $lenderAccountId
            ]);

            $available = $balance->available[0] ?? null;
            $pending = $balance->pending[0] ?? null;

            return response()->json([
                'success' => true,
                'data' => [
                    'lender_account_id' => $lenderAccountId,
                    'available' => [
                        'amount' => $available->amount ?? 0,
                        'currency' => $available->currency ?? 'usd',
                        'formatted' => '$' . number_format(($available->amount ?? 0) / 100, 2)
                    ],
                    'pending' => [
                        'amount' => $pending->amount ?? 0,
                        'currency' => $pending->currency ?? 'usd',
                        'formatted' => '$' . number_format(($pending->amount ?? 0) / 100, 2)
                    ],
                    'total_available' => ($available->amount ?? 0) + ($pending->amount ?? 0)
                ]
            ]);

        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'type' => $e->getStripeCode()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get funding history for lender
     * GET /api/funding/history/{lenderAccountId}
     */
    public function getFundingHistory(Request $request, string $lenderAccountId): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $startingAfter = $request->get('starting_after');

            $paymentIntents = PaymentIntent::all([
                'limit' => $limit,
                'starting_after' => $startingAfter
            ], [
                'stripe_account' => $lenderAccountId
            ]);

            $fundingHistory = [];
            foreach ($paymentIntents->data as $paymentIntent) {
                // Only include funding-related payment intents
                if (isset($paymentIntent->metadata->funding_type)) {
                    $fundingHistory[] = [
                        'payment_intent_id' => $paymentIntent->id,
                        'amount' => $paymentIntent->amount,
                        'currency' => $paymentIntent->currency,
                        'status' => $paymentIntent->status,
                        'description' => $paymentIntent->description,
                        'funding_type' => $paymentIntent->metadata->funding_type ?? 'unknown',
                        'bank_account_id' => $paymentIntent->metadata->bank_account_id ?? null,
                        'created' => date('Y-m-d H:i:s', $paymentIntent->created),
                        'succeeded' => $paymentIntent->status === 'succeeded'
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'lender_account_id' => $lenderAccountId,
                    'funding_history' => $fundingHistory,
                    'total_count' => count($fundingHistory),
                    'has_more' => $paymentIntents->has_more
                ]
            ]);

        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'type' => $e->getStripeCode()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if lender can fund account
     * GET /api/funding/can-fund/{lenderAccountId}
     */
    public function canFundAccount(Request $request, string $lenderAccountId): JsonResponse
    {
        try {
            $amount = $request->get('amount', 0);

            // Get current balance
            $balance = Balance::retrieve([], [
                'stripe_account' => $lenderAccountId
            ]);

            $available = $balance->available[0] ?? null;
            $currentBalance = $available->amount ?? 0;

            // Check if account has verified bank accounts
            $bankAccounts = Account::allExternalAccounts($lenderAccountId, [
                'object' => 'bank_account',
                'limit' => 1
            ]);

            $hasVerifiedBankAccount = false;
            foreach ($bankAccounts->data as $bankAccount) {
                if ($bankAccount->status === 'verified') {
                    $hasVerifiedBankAccount = true;
                    break;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'lender_account_id' => $lenderAccountId,
                    'can_fund' => $hasVerifiedBankAccount,
                    'current_balance' => [
                        'amount' => $currentBalance,
                        'currency' => $available->currency ?? 'usd',
                        'formatted' => '$' . number_format($currentBalance / 100, 2)
                    ],
                    'requested_amount' => [
                        'amount' => $amount,
                        'formatted' => '$' . number_format($amount / 100, 2)
                    ],
                    'has_verified_bank_account' => $hasVerifiedBankAccount,
                    'requirements' => [
                        'needs_verified_bank_account' => !$hasVerifiedBankAccount,
                        'needs_sufficient_balance' => $currentBalance < $amount
                    ]
                ]
            ]);

        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'type' => $e->getStripeCode()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
