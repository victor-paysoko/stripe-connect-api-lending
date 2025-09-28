# Stripe Connect Funding API Documentation

## Overview
This API provides endpoints for managing Stripe Connect funding operations, including funding lender accounts and initiating payouts to customers.

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentication
All endpoints require proper Stripe API keys configured in the environment variables.

## Endpoints

### 1. Fund Account (Test)
**POST** `/funding/fund-account-test`

Simulates funding a lender's Stripe connected account from their bank account.

#### Request Body
```json
{
    "lender_account_id": "acct_1S5P3K8q5fe8D08C",
    "amount": 100000,
    "currency": "usd",
    "bank_account": {
        "account_number": "000123456789",
        "routing_number": "110000000",
        "account_holder_name": "John Doe",
        "account_holder_type": "individual",
        "country": "US"
    },
    "description": "Initial funding for lending account"
}
```

#### Response
```json
{
    "message": "Test funding initiated successfully (simulated).",
    "payment_intent_id": "pi_test_1234567890",
    "status": "processing",
    "amount": 100000,
    "currency": "usd",
    "next_action": null,
    "simulated": true
}
```

### 2. Fund Account (Production)
**POST** `/funding/fund-account`

Funds a lender's Stripe connected account from their bank account using real Stripe API calls.

#### Request Body
```json
{
    "lender_account_id": "acct_1S5P3K8q5fe8D08C",
    "amount": 100000,
    "currency": "usd",
    "bank_account": {
        "account_number": "000123456789",
        "routing_number": "110000000",
        "account_holder_name": "John Doe",
        "account_holder_type": "individual",
        "country": "US"
    },
    "description": "Initial funding for lending account"
}
```

#### Response
```json
{
    "message": "Funding initiated successfully.",
    "payment_intent_id": "pi_1234567890",
    "status": "processing",
    "amount": 100000,
    "currency": "usd",
    "next_action": null
}
```

### 3. Get Balance
**GET** `/funding/balance/{lenderAccountId}`

Retrieves the current balance for a lender's Stripe connected account.

#### URL Parameters
- `lenderAccountId` (string): The Stripe connected account ID

#### Response
```json
{
    "available": 0,
    "pending": 0,
    "currency": "usd"
}
```

### 4. Get Funding History
**GET** `/funding/history/{lenderAccountId}`

Retrieves the funding history for a lender's Stripe connected account.

#### URL Parameters
- `lenderAccountId` (string): The Stripe connected account ID

#### Response
```json
{
    "history": [
        {
            "id": "pi_1234567890",
            "amount": 100000,
            "currency": "usd",
            "status": "succeeded",
            "description": "Initial funding",
            "created": 1695820800
        }
    ]
}
```

### 5. Check if Account Can Be Funded
**GET** `/funding/can-fund/{lenderAccountId}`

Checks if a lender's Stripe connected account can be funded.

#### URL Parameters
- `lenderAccountId` (string): The Stripe connected account ID

#### Response
```json
{
    "can_fund": true,
    "reason": "Account is active and can receive funds"
}
```

### 6. Real Payout (Creates Actual Stripe Objects)
**POST** `/payout/initiate`

**This endpoint creates real Stripe objects in both sandbox and production modes.** The payout will appear in your Stripe Connect dashboard and requires proper bank account verification.

- **Creates real PaymentMethod and PaymentIntent objects**
- **Appears in Stripe Connect dashboard**
- **May require microdeposit verification for new bank accounts**
- **Works in both sandbox and production environments**

Initiates a payout from a lender's Stripe connected account to a customer's bank account.

#### Request Body
```json
{
    "lender_account_id": "acct_1SBeq6K4camGic0r",
    "customer_account_id": "cus_T7ufQnqtX0ZBnZ",
    "amount": 500,
    "currency": "usd",
    "description": "Dynamic payout - auto-detects sandbox vs production",
    "bank_account": {
        "account_number": "000123456789",
        "routing_number": "110000000",
        "account_holder_name": "Jane Smith",
        "account_holder_type": "individual",
        "country": "US"
    }
}
```

#### Response (Success)
```json
{
    "success": true,
    "message": "Payout initiated successfully.",
    "data": {
        "payment_intent_id": "pi_3SBxXKK4camGic0r06MW8bg2",
        "payment_method_id": "pm_1SBxXKK4camGic0r06MW8bg2",
        "lender_account_id": "acct_1SBeq6K4camGic0r",
        "customer_account_id": "cus_T7ufQnqtX0ZBnZ",
        "amount": 700,
        "currency": "usd",
        "status": "succeeded",
        "bank_account": {
            "last4": "6789",
            "routing_number": "110000000",
            "account_holder_name": "Jane Smith",
            "account_holder_type": "individual"
        },
        "description": "Real Stripe payout test",
        "created": "2025-09-27 12:45:00",
        "note": "This payout will appear in your Stripe Connect dashboard"
    }
}
```

#### Response (Requires Verification)
```json
{
    "success": false,
    "message": "Payout requires additional verification.",
    "data": {
        "payment_intent_id": "pi_3SBxXKK4camGic0r06MW8bg2",
        "status": "requires_action",
        "amount": 700,
        "currency": "usd",
        "next_action": {
            "type": "verify_with_microdeposits",
            "verify_with_microdeposits": {
                "arrival_date": 1759129200,
                "hosted_verification_url": "https://payments.stripe.com/microdeposit/...",
                "microdeposit_type": "descriptor_code"
            }
        },
        "lender_account_id": "acct_1SBeq6K4camGic0r",
        "customer_account_id": "cus_T7ufQnqtX0ZBnZ",
        "note": "Microdeposit verification required for new bank accounts."
    }
}
```

## Error Responses

### Validation Error (422)
```json
{
    "errors": {
        "lender_account_id": ["The lender account id field is required."],
        "amount": ["The amount must be at least 50."]
    }
}
```

### Stripe API Error (400)
```json
{
    "success": false,
    "error": "Your card was declined.",
    "type": "card_error",
    "decline_code": "generic_decline"
}
```

### Server Error (500)
```json
{
    "error": "An unexpected error occurred: Internal server error"
}
```

## Testing with cURL

### Test Funding
```bash
curl -X POST http://127.0.0.1:8000/api/funding/fund-account-test \
  -H "Content-Type: application/json" \
  -d '{
    "lender_account_id": "acct_1S5P3K8q5fe8D08C",
    "amount": 100000,
    "currency": "usd",
    "bank_account": {
      "account_number": "000123456789",
      "routing_number": "110000000",
      "account_holder_name": "John Doe",
      "account_holder_type": "individual",
      "country": "US"
    },
    "description": "Test funding"
  }'
```

### Sandbox Payout (Recommended for Testing)
```bash
curl -X POST http://127.0.0.1:8000/api/payout/initiate-test \
  -H "Content-Type: application/json" \
  -d '{
    "lender_account_id": "acct_1S5P3K8q5fe8D08C",
    "customer_account_id": "cus_test123456789",
    "amount": 50000,
    "currency": "usd",
    "description": "Sandbox payout test",
    "bank_account": {
      "account_number": "000987654321",
      "routing_number": "110000000",
      "account_holder_name": "Jane Smith",
      "account_holder_type": "individual",
      "country": "US"
    }
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Payout initiated successfully (TEST MODE)",
  "data": {
    "payment_intent_id": "pi_payout_test_1eb0059f91a4",
    "payment_method_id": "pm_payout_test_162fdaad5cdc",
    "lender_account_id": "acct_1S5P3K8q5fe8D08C",
    "customer_account_id": "cus_test123456789",
    "amount": 50000,
    "currency": "usd",
    "status": "processing",
    "bank_account": {
      "last4": "6789",
      "routing_number": "110000000",
      "account_holder_name": "Jane Smith",
      "account_holder_type": "individual"
    },
    "description": "Sandbox payout test",
    "created": "2025-09-27 11:59:11",
    "test_mode": true
  }
}
```

### Production Payout (Requires Stripe Connect Permissions & Microdeposit Verification)
```bash
curl -X POST http://127.0.0.1:8000/api/payout/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "lender_account_id": "acct_1S5P3K8q5fe8D08C",
    "customer_account_id": "cus_test123456789",
    "amount": 50000,
    "currency": "usd",
    "description": "Production payout",
    "bank_account": {
      "account_number": "000987654321",
      "routing_number": "110000000",
      "account_holder_name": "Jane Smith",
      "account_holder_type": "individual",
      "country": "US"
    }
  }'
```

**Note:** Production endpoint requires microdeposit verification with codes like `SM1234`, `SM0001`, etc.

### Get Balance
```bash
curl -X GET http://127.0.0.1:8000/api/funding/balance/acct_1S5P3K8q5fe8D08C
```

### Get History
```bash
curl -X GET http://127.0.0.1:8000/api/funding/history/acct_1S5P3K8q5fe8D08C
```

## Notes

- All amounts are in cents (e.g., 100000 = $1000.00)
- Minimum funding amount is $0.50 (50 cents)
- Minimum payout amount is $1.00 (100 cents)
- All bank accounts must be US-based
- Test account ID `acct_1S5P3K8q5fe8D08C` is used for testing purposes
- ACH transfers typically take 3-5 business days to complete
- Microdeposit verification may be required for new bank accounts
