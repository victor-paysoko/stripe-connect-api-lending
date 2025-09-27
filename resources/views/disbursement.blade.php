<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paysoko Stripe Connect B2C - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Alpine.js for interactivity -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --bs-primary: #635bff;
            --bs-primary-rgb: 99, 91, 255;
            --bs-secondary: #6c757d;
            --bs-success: #00d924;
            --bs-info: #00aaff;
            --bs-warning: #ffb800;
            --bs-danger: #ff3b30;
            --bs-light: #f8f9fa;
            --bs-dark: #1a1a1a;
            --bs-gradient: linear-gradient(135deg, #635bff 0%, #00d924 100%);
            --bs-gradient-secondary: linear-gradient(135deg, #00aaff 0%, #635bff 100%);
            --bs-gradient-success: linear-gradient(135deg, #00d924 0%, #00aaff 100%);
        }

        [data-bs-theme="dark"] {
            --bs-body-bg: #1a1a1a;
            --bs-body-color: #ffffff;
            --bs-emphasis-color: #ffffff;
            --bs-secondary-color: rgba(255, 255, 255, 0.75);
            --bs-tertiary-color: rgba(255, 255, 255, 0.5);
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        [data-bs-theme="dark"] body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        [data-bs-theme="dark"] .glass-effect {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(2deg); }
        }

        .wizard-step {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .step-indicator {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .step-indicator:hover::before {
            left: 100%;
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .card-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.6s;
        }

        .card-hover:hover::before {
            left: 100%;
        }

        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        [data-bs-theme="dark"] .card-hover:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .navbar {
            background: rgba(255, 255, 255, 0.2) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        [data-bs-theme="dark"] .navbar {
            background: rgba(15, 23, 42, 0.6) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-control, .form-select {
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 0.2rem rgba(99, 91, 255, 0.25);
            background: rgba(255, 255, 255, 0.3);
        }

        [data-bs-theme="dark"] .form-control, [data-bs-theme="dark"] .form-select {
            background: rgba(15, 23, 42, 0.4);
            border-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        [data-bs-theme="dark"] .form-control:focus, [data-bs-theme="dark"] .form-select:focus {
            background: rgba(15, 23, 42, 0.6);
        }

        .btn {
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #635bff 0%, #00aaff 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(99, 91, 255, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 91, 255, 0.6);
        }

        .btn-success {
            background: linear-gradient(135deg, #00d924 0%, #00aaff 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(0, 217, 36, 0.4);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 217, 36, 0.6);
        }

        .amount-btn {
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .amount-btn:hover {
            transform: translateY(-1px);
        }

        .amount-btn.btn-primary {
            background: linear-gradient(135deg, #635bff 0%, #00aaff 100%);
            border: none;
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        [data-bs-theme="dark"] .input-group-text {
            background: rgba(15, 23, 42, 0.4);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .badge {
            border-radius: 20px;
            font-weight: 500;
        }

        .spinner-border {
            border-width: 3px;
        }

        .toast {
            border-radius: 12px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        [data-bs-theme="dark"] .toast {
            background: rgba(15, 23, 42, 0.8);
            border-color: rgba(255, 255, 255, 0.1);
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        .gradient-text {
            background: linear-gradient(135deg, #635bff 0%, #00d924 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .icon-glow {
            filter: drop-shadow(0 0 10px rgba(99, 91, 255, 0.5));
        }

        .container-fluid {
            max-width: 1400px;
        }

        .lead {
            font-weight: 400;
            line-height: 1.6;
        }

        .display-4 {
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .fw-bold {
            font-weight: 700 !important;
        }

        .fw-semibold {
            font-weight: 600 !important;
        }

        .fw-medium {
            font-weight: 500 !important;
        }
    </style>
</head>
<body class="bg-light" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', value => { localStorage.setItem('darkMode', value); document.documentElement.setAttribute('data-bs-theme', value ? 'dark' : 'light') })" :data-bs-theme="darkMode ? 'dark' : 'light'">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white glass-effect border-bottom sticky-top">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center me-3">
                    <div class="bg-primary rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                        <i class="bi bi-currency-dollar text-white"></i>
                    </div>
                    <h1 class="navbar-brand mb-0 fw-bold">Paysoko Stripe Connect B2C</h1>
                </div>
            </div>

            <div class="d-flex align-items-center">
                <!-- API Status -->
                <div class="d-none d-sm-flex align-items-center bg-success bg-opacity-10 rounded-pill px-3 py-1 me-3">
                    <div class="bg-success rounded-circle me-2" style="width: 8px; height: 8px; animation: pulse 2s infinite;"></div>
                    <span class="text-success fw-medium small">Paysoko Stripe Connect B2C</span>
                </div>

                <!-- Dark Mode Toggle -->
                <button
                    @click="darkMode = !darkMode"
                    class="btn btn-outline-secondary btn-sm"
                    :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                >
                    <i x-show="!darkMode" class="bi bi-moon-fill"></i>
                    <i x-show="darkMode" class="bi bi-sun-fill text-warning"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- Header -->
                <div class="text-center mb-5">
                    <div class="bg-primary rounded-4 d-inline-flex align-items-center justify-content-center mb-4 floating-animation icon-glow" style="width: 80px; height: 80px;">
                        <i class="bi bi-currency-dollar text-white" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="display-4 fw-bold mb-3 gradient-text">Fund Lender Account</h2>
                    <p class="lead text-muted mx-auto" style="max-width: 600px;">Seamlessly transfer funds from a bank account to a lender's Stripe connected account with our secure and modern platform</p>
                </div>

                <!-- Main Content Layout -->
                <div class="row g-4">
                    <!-- Funding Wizard Section -->
                    <div class="col-12 col-lg-8">
                        <!-- Funding Wizard -->
                        <div class="card glass-effect card-hover shadow-lg h-100" style="border-radius: 20px;">
                            <div class="card-body p-5">
                                <!-- Wizard Header -->
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="bi bi-file-earmark-text text-white"></i>
                                        </div>
                                        <h3 class="card-title mb-0">Funding Wizard</h3>
                                    </div>

                                    <!-- Step Indicator -->
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex align-items-center me-2">
                                            <div id="step1-indicator" class="step-indicator bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.875rem; font-weight: 500;">1</div>
                                            <span class="small text-muted">Account</span>
                                        </div>
                                        <div class="bg-secondary bg-opacity-25 me-2" style="width: 32px; height: 2px;"></div>
                                        <div class="d-flex align-items-center">
                                            <div id="step2-indicator" class="step-indicator bg-secondary bg-opacity-25 text-muted rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.875rem; font-weight: 500;">2</div>
                                            <span class="small text-muted">Bank</span>
                                        </div>
                                    </div>
                                </div>

                                <form id="fundingForm">
                                    <!-- Step 1: Account Information -->
                                    <div id="step1" class="wizard-step">
                                        <div class="text-center mb-4">
                                            <h4 class="h5 fw-semibold mb-2">Account Information</h4>
                                            <p class="text-muted small">Enter the lender's account details and funding amount</p>
                                        </div>

                                        <div class="row g-4 mb-4">
                                            <div class="col-12">
                                                <label for="lender_account_id" class="form-label fw-semibold">
                                                    <i class="bi bi-person-circle me-2"></i>Lender Account ID
                                                </label>
                                                <div class="input-group input-group-lg">
                                                    <span class="input-group-text bg-primary bg-opacity-10 border-primary border-opacity-25">
                                                        <i class="bi bi-person-badge text-primary"></i>
                                                    </span>
                                                    <input
                                                        type="text"
                                                        id="lender_account_id"
                                                        name="lender_account_id"
                                                        value="acct_1S5P3K8q5fe8D08C"
                                                        class="form-control form-control-lg"
                                                        placeholder="acct_1234567890"
                                                        required
                                                    >
                                                </div>
                                                <div class="form-text">Enter the Stripe Connect account ID for the lender</div>
                                            </div>

                                            <div class="col-12">
                                                <label for="amount" class="form-label fw-semibold">
                                                    <i class="bi bi-currency-dollar me-2"></i>Amount (USD)
                                                </label>
                                                <div class="input-group input-group-lg">
                                                    <span class="input-group-text bg-primary bg-opacity-10 border-primary border-opacity-25">$</span>
                                                    <input
                                                        type="number"
                                                        id="amount"
                                                        name="amount"
                                                        min="100"
                                                        step="0.01"
                                                        class="form-control form-control-lg"
                                                        placeholder="1000.00"
                                                        value="1000.00"
                                                        required
                                                    >
                                                </div>
                                                <div class="form-text">Minimum amount: $1.00 (100 cents)</div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="description" class="form-label fw-semibold">
                                                <i class="bi bi-chat-text me-2"></i>Description
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text bg-primary bg-opacity-10 border-primary border-opacity-25">
                                                    <i class="bi bi-file-text text-primary"></i>
                                                </span>
                                                <input
                                                    type="text"
                                                    id="description"
                                                    name="description"
                                                    class="form-control form-control-lg"
                                                    placeholder="Initial funding for lending account"
                                                    value="Initial funding for lending account"
                                                >
                                            </div>
                                        </div>

                                        <!-- Quick Amount Buttons -->
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold mb-3">
                                                <i class="bi bi-lightning me-2"></i>Quick Amount
                                            </label>
                                            <div class="row g-3">
                                                <div class="col-6 col-md-3">
                                                    <button type="button" class="amount-btn btn btn-outline-primary w-100 py-3" data-amount="100">
                                                        <i class="bi bi-currency-dollar me-2"></i>$100
                                                    </button>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <button type="button" class="amount-btn btn btn-outline-primary w-100 py-3" data-amount="500">
                                                        <i class="bi bi-currency-dollar me-2"></i>$500
                                                    </button>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <button type="button" class="amount-btn btn btn-primary w-100 py-3" data-amount="1000">
                                                        <i class="bi bi-currency-dollar me-2"></i>$1,000
                                                    </button>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <button type="button" class="amount-btn btn btn-outline-primary w-100 py-3" data-amount="5000">
                                                        <i class="bi bi-currency-dollar me-2"></i>$5,000
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 2: Bank Account Information -->
                                    <div id="step2" class="wizard-step d-none">
                                        <div class="text-center mb-4">
                                            <h4 class="h5 fw-semibold mb-2">Bank Account Information</h4>
                                            <p class="text-muted small">Provide the bank account details for funding</p>
                                        </div>

                                        <div class="row g-4 mb-4">
                                            <!-- Account Number -->
                                            <div class="col-12">
                                                <label for="account_number" class="form-label fw-semibold">
                                                    <i class="bi bi-credit-card me-2"></i>Account Number
                                                </label>
                                                <div class="input-group input-group-lg">
                                                    <span class="input-group-text bg-primary bg-opacity-10 border-primary border-opacity-25">
                                                        <i class="bi bi-credit-card text-primary"></i>
                                                    </span>
                                                    <input
                                                        type="text"
                                                        id="account_number"
                                                        name="account_number"
                                                        class="form-control form-control-lg"
                                                        placeholder="000123456789"
                                                        required
                                                    >
                                                </div>
                                            </div>

                                            <!-- Routing Number -->
                                            <div class="col-12">
                                                <label for="routing_number" class="form-label fw-semibold">
                                                    <i class="bi bi-bank me-2"></i>Routing Number
                                                </label>
                                                <div class="input-group input-group-lg">
                                                    <span class="input-group-text bg-primary bg-opacity-10 border-primary border-opacity-25">
                                                        <i class="bi bi-bank text-primary"></i>
                                                    </span>
                                                    <input
                                                        type="text"
                                                        id="routing_number"
                                                        name="routing_number"
                                                        class="form-control form-control-lg"
                                                        placeholder="110000000"
                                                        required
                                                    >
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-4 mb-4">
                                            <!-- Account Holder Name -->
                                            <div class="col-12">
                                                <label for="account_holder_name" class="form-label fw-semibold">
                                                    <i class="bi bi-person me-2"></i>Account Holder Name
                                                </label>
                                                <div class="input-group input-group-lg">
                                                    <span class="input-group-text bg-primary bg-opacity-10 border-primary border-opacity-25">
                                                        <i class="bi bi-person text-primary"></i>
                                                    </span>
                                                    <input
                                                        type="text"
                                                        id="account_holder_name"
                                                        name="account_holder_name"
                                                        class="form-control form-control-lg"
                                                        placeholder="John Doe"
                                                        required
                                                    >
                                                </div>
                                            </div>

                                            <!-- Account Holder Type -->
                                            <div class="col-12">
                                                <label for="account_holder_type" class="form-label fw-semibold">
                                                    <i class="bi bi-building me-2"></i>Account Type
                                                </label>
                                                <div class="input-group input-group-lg">
                                                    <span class="input-group-text bg-primary bg-opacity-10 border-primary border-opacity-25">
                                                        <i class="bi bi-building text-primary"></i>
                                                    </span>
                                                    <select
                                                        id="account_holder_type"
                                                        name="account_holder_type"
                                                        class="form-select form-select-lg"
                                                        required
                                                    >
                                                        <option value="individual">Individual</option>
                                                        <option value="company">Company</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Wizard Navigation -->
                                    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                                        <button
                                            type="button"
                                            id="prevBtn"
                                            class="btn btn-outline-secondary d-none"
                                        >
                                            <i class="bi bi-chevron-left me-2"></i>Previous
                                        </button>

                                        <div class="flex-grow-1"></div>

                                        <button
                                            type="button"
                                            id="nextBtn"
                                            class="btn btn-primary btn-lg"
                                        >
                                            Next<i class="bi bi-chevron-right ms-2"></i>
                                        </button>

                                        <button
                                            type="submit"
                                            id="submitBtn"
                                            class="btn btn-success btn-lg d-none"
                                        >
                                            <i class="bi bi-currency-dollar me-2"></i>Fund Account
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information & Status Section -->
                    <div class="col-12 col-lg-4">
                        <div class="d-flex flex-column gap-4 h-100">
                            <!-- Account Balance -->
                            <div class="card glass-effect card-hover shadow-lg" style="border-radius: 16px;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="bg-success rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="bi bi-currency-dollar text-white"></i>
                                        </div>
                                        <h5 class="card-title mb-0">Account Balance</h5>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted small mb-1">Available Balance</p>
                                            <h3 class="fw-bold mb-0" id="availableBalance">$0.00</h3>
                                        </div>
                                        <button
                                            onclick="checkBalance()"
                                            class="btn btn-outline-secondary btn-sm"
                                        >
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Funding History -->
                            <div class="card glass-effect card-hover shadow-lg" style="border-radius: 16px;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-4">
                                        <h5 class="card-title mb-0">Recent Transactions</h5>
                                    </div>
                                    <div id="fundingHistory">
                                        <div class="text-center text-muted py-4">
                                            <p class="fw-medium mb-1">No transactions yet</p>
                                            <p class="small">Fund an account to see history</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- API Status -->
                            <div class="card glass-effect card-hover shadow-lg" style="border-radius: 16px;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="bg-warning rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="bi bi-check-circle text-white"></i>
                                        </div>
                                        <h5 class="card-title mb-0">API Status</h5>
                                    </div>
                                    <div class="d-flex flex-column gap-3">
                                        <div class="d-flex justify-content-between align-items-center p-3 bg-primary bg-opacity-10 rounded-3" style="border: 1px solid rgba(99, 91, 255, 0.2);">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle me-3" style="width: 10px; height: 10px;"></div>
                                                <span class="small fw-medium">Funding API</span>
                                            </div>
                                            <button class="btn btn-primary btn-sm px-3 py-1" onclick="initiateFundingAPI()">
                                                <i class="bi bi-play-fill me-1"></i>Initiate
                                            </button>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center p-3 bg-info bg-opacity-10 rounded-3" style="border: 1px solid rgba(0, 170, 255, 0.2);">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info rounded-circle me-3" style="width: 10px; height: 10px;"></div>
                                                <span class="small fw-medium">Balance API</span>
                                            </div>
                                            <button class="btn btn-info btn-sm px-3 py-1" onclick="initiateBalanceAPI()">
                                                <i class="bi bi-play-fill me-1"></i>Initiate
                                            </button>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center p-3 bg-warning bg-opacity-10 rounded-3" style="border: 1px solid rgba(255, 184, 0, 0.2);">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning rounded-circle me-3" style="width: 10px; height: 10px;"></div>
                                                <span class="small fw-medium">History API</span>
                                            </div>
                                            <button class="btn btn-warning btn-sm px-3 py-1" onclick="initiateHistoryAPI()">
                                                <i class="bi bi-play-fill me-1"></i>Initiate
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none" style="z-index: 9999;">
        <div class="position-relative top-50 start-50 translate-middle">
            <div class="card glass-effect shadow-lg" style="width: 400px; border-radius: 20px;">
                <div class="card-body text-center p-5">
                    <div class="spinner-border text-primary mb-4" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="card-title fw-bold">Processing Request</h5>
                    <p class="card-text text-muted">Please wait while we process your funding request...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- API Documentation Modal -->
    <div class="modal fade" id="apiModal" tabindex="-1" aria-labelledby="apiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content glass-effect" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="apiModalLabel">API Documentation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">Endpoint</h6>
                        <div class="bg-dark text-light p-3 rounded-3">
                            <code id="apiEndpoint" class="text-info"></code>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">Method</h6>
                        <span id="apiMethod" class="badge bg-primary fs-6"></span>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">Request Body</h6>
                        <div class="bg-light p-3 rounded-3">
                            <pre id="apiRequestBody" class="mb-0" style="white-space: pre-wrap; font-size: 0.875rem;"></pre>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">Response Example</h6>
                        <div class="bg-light p-3 rounded-3">
                            <pre id="apiResponse" class="mb-0" style="white-space: pre-wrap; font-size: 0.875rem;"></pre>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="copyToClipboard()">
                            <i class="bi bi-clipboard me-2"></i>Copy Request Body
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="messageContainer" class="position-fixed top-0 end-0 p-4 d-none" style="z-index: 9999;">
        <div id="messageContent" class="toast show glass-effect shadow-lg" role="alert" style="min-width: 350px; border-radius: 16px;">
            <div class="toast-header border-0 pb-2">
                <div id="messageIconContainer" class="rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i id="messageIcon" class="bi bi-check-circle-fill text-success"></i>
                </div>
                <strong id="messageText" class="me-auto fw-semibold">Message</strong>
                <button type="button" class="btn-close" onclick="hideMessage()"></button>
            </div>
            <div class="toast-body pt-0">
                <p id="messageSubtext" class="mb-0 text-muted"></p>
            </div>
        </div>
    </div>

    <!-- Footer Help Section -->
    <footer class="mt-5" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #bae6fd 100%);">
        <div class="container-fluid py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <!-- Help Header -->
                        <div class="text-center mb-4">
                            <div class="bg-success rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-question-circle text-white" style="font-size: 1.8rem;"></i>
                            </div>
                            <h3 class="fw-bold mb-2 text-success">Funding Help</h3>
                            <p class="text-muted">Quick answers for common funding questions</p>
                        </div>

                        <!-- Help Tabs -->
                        <div class="row g-2 mb-3">
                        <div class="col-6 col-md-3">
                            <button class="help-tab btn btn-success w-100 py-2" data-target="process">
                                <i class="bi bi-arrow-right-circle me-2"></i>
                                <span class="small">Process</span>
                            </button>
                        </div>
                            <div class="col-6 col-md-3">
                                <button class="help-tab btn btn-outline-success w-100 py-2" data-target="timing">
                                    <i class="bi bi-clock me-2"></i>
                                    <span class="small">Timing</span>
                                </button>
                            </div>
                            <div class="col-6 col-md-3">
                                <button class="help-tab btn btn-outline-success w-100 py-2" data-target="requirements">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <span class="small">Requirements</span>
                                </button>
                            </div>
                            <div class="col-6 col-md-3">
                                <button class="help-tab btn btn-outline-success w-100 py-2" data-target="troubleshooting">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <span class="small">Issues</span>
                                </button>
                            </div>
                        </div>

                        <!-- Help Content -->
                        <div class="row g-3">
                            <!-- Process -->
                            <div class="col-12 help-content" id="process" style="display: block;">
                                <div class="card glass-effect shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-arrow-right-circle text-success me-2"></i>
                                            <h6 class="card-title mb-0 text-success">Funding Process</h6>
                                        </div>
                                        <p class="text-muted mb-0 small">1. Enter lender account ID and amount 2. Provide bank details 3. ACH transfer initiates 4. Funds appear in 3-5 business days</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Timing -->
                            <div class="col-12 help-content" id="timing" style="display: none;">
                                <div class="card glass-effect shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-clock text-success me-2"></i>
                                            <h6 class="card-title mb-0 text-success">Processing Times</h6>
                                        </div>
                                        <p class="text-muted mb-0 small">ACH transfers: 3-5 business days • Bank verification: 1-2 business days • Minimum amount: $1.00</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Requirements -->
                            <div class="col-12 help-content" id="requirements" style="display: none;">
                                <div class="card glass-effect shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-check-circle text-success me-2"></i>
                                            <h6 class="card-title mb-0 text-success">Requirements</h6>
                                        </div>
                                        <p class="text-muted mb-0 small">Valid Stripe Connect account • US bank account • Correct routing/account numbers • Account holder name matches bank records</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Troubleshooting -->
                            <div class="col-12 help-content" id="troubleshooting" style="display: none;">
                                <div class="card glass-effect shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-exclamation-triangle text-success me-2"></i>
                                            <h6 class="card-title mb-0 text-success">Common Issues</h6>
                                        </div>
                                        <p class="text-muted mb-0 small">Verify account details • Ensure bank supports ACH • Check account balance • Contact bank if transfer fails</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="text-center mt-3">
                            <div class="row g-2 justify-content-center">
                                <div class="col-auto">
                                    <a href="#" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-book me-1"></i>Docs
                                    </a>
                                </div>
                                <div class="col-auto">
                                    <a href="#" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-headset me-1"></i>Support
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="container-fluid py-3" style="background: rgba(0, 0, 0, 0.05); border-top: 1px solid rgba(0, 0, 0, 0.1);">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0 text-muted small">
                            <i class="bi bi-shield-check me-1"></i>
                            Secure funding powered by Stripe Connect
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-0 text-muted small">
                            © 2025 Paysoko Stripe Connect B2C. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // API Configuration
        const API_BASE_URL = 'http://127.0.0.1:8000/api';

        // Wizard Configuration
        let currentStep = 1;
        const totalSteps = 2;

        // Form submission handler
        document.getElementById('fundingForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = {
                lender_account_id: formData.get('lender_account_id'),
                amount: Math.round(parseFloat(formData.get('amount')) * 100), // Convert to cents
                currency: 'usd',
                bank_account: {
                    account_number: formData.get('account_number'),
                    routing_number: formData.get('routing_number'),
                    account_holder_name: formData.get('account_holder_name'),
                    account_holder_type: formData.get('account_holder_type'),
                    country: 'US'
                },
                description: formData.get('description')
            };

            try {
                showLoading();
                const response = await fetch(`${API_BASE_URL}/funding/fund-account-test`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                hideLoading();

                if (result.success) {
                    showMessage('success', 'Funding Successful!', `Account funded with $${(data.amount / 100).toFixed(2)}`);
                    // Refresh balance and history
                    checkBalance();
                    loadFundingHistory();
                } else {
                    showMessage('error', 'Funding Failed', result.error || 'An error occurred');
                }
            } catch (error) {
                hideLoading();
                showMessage('error', 'Network Error', 'Unable to connect to the API');
                console.error('Error:', error);
            }
        });

        // Check balance function
        async function checkBalance() {
            const lenderAccountId = document.getElementById('lender_account_id').value;
            if (!lenderAccountId) return;

            try {
                const response = await fetch(`${API_BASE_URL}/funding/balance/${lenderAccountId}`);
                const result = await response.json();

                if (result.available !== undefined) {
                    document.getElementById('availableBalance').textContent = `$${(result.available / 100).toFixed(2)}`;
                }
            } catch (error) {
                console.error('Error checking balance:', error);
            }
        }

        // Load funding history
        async function loadFundingHistory() {
            const lenderAccountId = document.getElementById('lender_account_id').value;
            if (!lenderAccountId) return;

            try {
                const response = await fetch(`${API_BASE_URL}/funding/history/${lenderAccountId}`);
                const result = await response.json();

                const historyContainer = document.getElementById('fundingHistory');
                if (result.history && result.history.length > 0) {
                    historyContainer.innerHTML = result.history.map(transaction => `
                        <div class="d-flex justify-content-between align-items-center p-3 mb-3 bg-light bg-opacity-50 rounded-3 border">
                            <div>
                                <p class="fw-medium mb-1 text-dark">${transaction.description || 'Funding'}</p>
                                <p class="small text-muted mb-0">${new Date(transaction.created * 1000).toLocaleDateString()}</p>
                            </div>
                            <div class="text-end">
                                <p class="fw-semibold mb-1 text-dark">$${(transaction.amount / 100).toFixed(2)}</p>
                                <span class="badge ${
                                    transaction.status === 'succeeded' ? 'bg-success' :
                                    transaction.status === 'processing' ? 'bg-warning' :
                                    'bg-danger'
                                }">
                                    ${transaction.status}
                                </span>
                            </div>
                        </div>
                    `).join('');
                } else {
                    historyContainer.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <p class="fw-medium mb-1">No transactions yet</p>
                            <p class="small">Fund an account to see history</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading history:', error);
            }
        }

        // Utility functions
        function showLoading() {
            document.getElementById('loadingOverlay').classList.remove('d-none');
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').classList.add('d-none');
        }

        function showMessage(type, title, message) {
            const container = document.getElementById('messageContainer');
            const iconContainer = document.getElementById('messageIconContainer');
            const icon = document.getElementById('messageIcon');
            const text = document.getElementById('messageText');
            const subtext = document.getElementById('messageSubtext');

            if (type === 'success') {
                iconContainer.className = 'rounded-3 me-3 d-flex align-items-center justify-content-center bg-success bg-opacity-10';
                icon.className = 'bi bi-check-circle-fill text-success';
                container.classList.remove('d-none');
            } else if (type === 'info') {
                iconContainer.className = 'rounded-3 me-3 d-flex align-items-center justify-content-center bg-info bg-opacity-10';
                icon.className = 'bi bi-info-circle-fill text-info';
                container.classList.remove('d-none');
            } else {
                iconContainer.className = 'rounded-3 me-3 d-flex align-items-center justify-content-center bg-danger bg-opacity-10';
                icon.className = 'bi bi-exclamation-triangle-fill text-danger';
                container.classList.remove('d-none');
            }

            text.textContent = title;
            subtext.textContent = message;

            // Auto-hide after 5 seconds
            setTimeout(hideMessage, 5000);
        }

        function hideMessage() {
            document.getElementById('messageContainer').classList.add('d-none');
        }

        // Wizard Navigation Functions
        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.wizard-step').forEach(stepEl => {
                stepEl.classList.add('d-none');
            });

            // Show current step
            document.getElementById(`step${step}`).classList.remove('d-none');

            // Update step indicators
            for (let i = 1; i <= totalSteps; i++) {
                const indicator = document.getElementById(`step${i}-indicator`);
                if (i <= step) {
                    indicator.className = 'step-indicator bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2';
                    indicator.style.width = '32px';
                    indicator.style.height = '32px';
                    indicator.style.fontSize = '0.875rem';
                    indicator.style.fontWeight = '500';
                } else {
                    indicator.className = 'step-indicator bg-secondary bg-opacity-25 text-muted rounded-circle d-flex align-items-center justify-content-center me-2';
                    indicator.style.width = '32px';
                    indicator.style.height = '32px';
                    indicator.style.fontSize = '0.875rem';
                    indicator.style.fontWeight = '500';
                }
            }

            // Update navigation buttons
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');

            if (step === 1) {
                prevBtn.classList.add('d-none');
            } else {
                prevBtn.classList.remove('d-none');
            }

            if (step === totalSteps) {
                nextBtn.classList.add('d-none');
                submitBtn.classList.remove('d-none');
            } else {
                nextBtn.classList.remove('d-none');
                submitBtn.classList.add('d-none');
            }
        }

        function nextStep() {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        }

        function validateCurrentStep() {
            const currentStepEl = document.getElementById(`step${currentStep}`);
            const requiredFields = currentStepEl.querySelectorAll('[required]');

            for (let field of requiredFields) {
                if (!field.value.trim()) {
                    field.focus();
                    showMessage('error', 'Validation Error', `Please fill in the ${field.previousElementSibling.textContent} field`);
                    return false;
                }
            }
            return true;
        }

        // Wizard Event Listeners
        document.getElementById('nextBtn').addEventListener('click', nextStep);
        document.getElementById('prevBtn').addEventListener('click', prevStep);

        // Quick amount buttons
        document.querySelectorAll('.amount-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const amount = this.dataset.amount;
                document.getElementById('amount').value = amount;

                // Update button styles
                document.querySelectorAll('.amount-btn').forEach(b => {
                    b.className = 'amount-btn btn btn-outline-secondary w-100';
                });
                this.className = 'amount-btn btn btn-primary w-100';
            });
        });

        // API Documentation Functions
        function initiateFundingAPI() {
            const apiData = {
                endpoint: `${API_BASE_URL}/funding/fund-account-test`,
                method: 'POST',
                requestBody: {
                    lender_account_id: "acct_1S5P3K8q5fe8D08C",
                    amount: 100000,
                    currency: "usd",
                    bank_account: {
                        account_number: "000123456789",
                        routing_number: "110000000",
                        account_holder_name: "John Doe",
                        account_holder_type: "individual",
                        country: "US"
                    },
                    description: "Initial funding for lending account"
                },
                response: {
                    message: "Test funding initiated successfully (simulated).",
                    payment_intent_id: "pi_test_1234567890",
                    status: "processing",
                    amount: 100000,
                    currency: "usd",
                    next_action: null,
                    simulated: true
                }
            };
            showApiDocumentation(apiData);
        }

        function initiateBalanceAPI() {
            const apiData = {
                endpoint: `${API_BASE_URL}/funding/balance/{lenderAccountId}`,
                method: 'GET',
                requestBody: null,
                response: {
                    available: 0,
                    pending: 0,
                    currency: "usd"
                }
            };
            showApiDocumentation(apiData);
        }

        function initiateHistoryAPI() {
            const apiData = {
                endpoint: `${API_BASE_URL}/funding/history/{lenderAccountId}`,
                method: 'GET',
                requestBody: null,
                response: {
                    history: [
                        {
                            id: "pi_1234567890",
                            amount: 100000,
                            currency: "usd",
                            status: "succeeded",
                            description: "Initial funding",
                            created: 1695820800
                        }
                    ]
                }
            };
            showApiDocumentation(apiData);
        }

        function showApiDocumentation(apiData) {
            document.getElementById('apiEndpoint').textContent = apiData.endpoint;
            document.getElementById('apiMethod').textContent = apiData.method;
            document.getElementById('apiRequestBody').textContent = apiData.requestBody ? JSON.stringify(apiData.requestBody, null, 2) : 'No request body required';
            document.getElementById('apiResponse').textContent = JSON.stringify(apiData.response, null, 2);

            const modal = new bootstrap.Modal(document.getElementById('apiModal'));
            modal.show();
        }

        function copyToClipboard() {
            const requestBody = document.getElementById('apiRequestBody').textContent;
            navigator.clipboard.writeText(requestBody).then(() => {
                showMessage('success', 'Copied!', 'Request body copied to clipboard');
            }).catch(() => {
                showMessage('error', 'Error', 'Failed to copy to clipboard');
            });
        }

        // Help Tab Functionality
        function initializeHelpTabs() {
            const helpTabs = document.querySelectorAll('.help-tab');
            const helpContents = document.querySelectorAll('.help-content');

            helpTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');

                    // Remove active class from all tabs
                    helpTabs.forEach(t => {
                        t.classList.remove('btn-success');
                        t.classList.add('btn-outline-success');
                    });

                    // Add active class to clicked tab
                    this.classList.remove('btn-outline-success');
                    this.classList.add('btn-success');

                    // Hide all content
                    helpContents.forEach(content => {
                        content.style.display = 'none';
                    });

                    // Show target content
                    const targetContent = document.getElementById(target);
                    if (targetContent) {
                        targetContent.style.display = 'block';
                    }
                });
            });

            // Show first content by default
            if (helpContents.length > 0) {
                helpContents[0].style.display = 'block';
                helpTabs[0].classList.remove('btn-outline-success');
                helpTabs[0].classList.add('btn-success');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            showStep(1);
            checkBalance();
            loadFundingHistory();
            initializeHelpTabs();
        });
    </script>
</body>
</html>
