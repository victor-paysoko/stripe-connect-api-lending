<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Payment - {{ config('app.name') }}</title>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'stripe': '#635bff',
                        'stripe-dark': '#4c44d4'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="#" class="text-xl font-bold text-gray-900">{{ config('app.name') }}</a>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-stripe rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">U</span>
                        </div>
                        <span class="text-gray-700">User Name</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Make Payment</h1>
                <p class="text-gray-600">Send secure payments using Stripe Connect</p>
            </div>

            <!-- Alert Messages -->
            <div id="alert-container"></div>

            <!-- Payment Form Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-stripe to-purple-600 px-6 py-8">
                    <div class="flex items-center justify-center">
                        <div class="bg-white/20 rounded-full p-3 mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-white">Secure Payment</h2>
                    </div>
                    <p class="text-purple-100 text-center mt-2">Powered by Stripe Connect</p>
                </div>

                <!-- Form Content -->
                <form id="payment-form" class="p-6 space-y-6">
                    <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">
                    <input type="hidden" id="borrower_id" value="{{ $borower_id }}">
                    <input type="hidden" id="loan_id" value="{{ $loan_id }}">

                    <!-- FI Account Selection -->
                    <div>
                        <label for="fi_account_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Financial Institution Account <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select required name="fi_account_id" id="fi_account_id"
                                class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-stripe focus:border-transparent transition-colors bg-white disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <option value="">Loading accounts...</option>
                            </select>
                            <!-- Loading indicator -->
                            <div id="accounts-loading" class="absolute right-3 top-3">
                                <svg class="animate-spin w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke-width="2" stroke-opacity="0.25" />
                                    <path stroke-linecap="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Select the connected account to receive this payment</p>
                    </div>

                    <!-- Account Info Display -->
                    <div id="account-info" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-blue-900">Account Details</h4>
                                <div class="text-sm text-blue-800 mt-1">
                                    <p><span class="font-medium">Name:</span> <span id="account-name">-</span></p>
                                    <p><span class="font-medium">Email:</span> <span id="account-email">-</span></p>
                                    <p><span class="font-medium">Country:</span> <span id="account-country">-</span></p>
                                    <p><span class="font-medium">Currency:</span> <span id="account-currency">-</span></p>
                                    <div class="flex items-center mt-2">
                                        <span class="font-medium">Status:</span>
                                        <span id="account-status" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            <!-- Status will be populated here -->
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Amount & Currency -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-500 text-lg">$</span>
                                <input type="number" id="amount" name="amount" required
                                    class="w-full pl-10 pr-3 py-2 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-stripe focus:border-transparent transition-colors"
                                    placeholder="0.00" step="0.01" min="0.50" max="999999">
                            </div>
                        </div>

                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                                Currency <span class="text-red-500">*</span>
                            </label>
                            <select required name="currency" id="currency"
                                class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-stripe focus:border-transparent transition-colors bg-white">
                                <option value="usd">USD ($)</option>
                                <option value="eur">EUR (€)</option>
                                <option value="gbp">GBP (£)</option>
                            </select>
                        </div>
                    </div>
   <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Customer Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required maxlength="255"
                            class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-stripe focus:border-transparent transition-colors"
                            placeholder="customer name">
                    </div>
                       <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                           Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" required name="email" maxlength="255"
                            class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-stripe focus:border-transparent transition-colors"
                            placeholder="customer email">
                    </div>
                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description (Optional)
                        </label>
                        <input type="text" id="description" name="description" maxlength="255"
                            class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-stripe focus:border-transparent transition-colors"
                            placeholder="What is this payment for?">
                    </div>

                    <!-- Card Details Section -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Payment Details <span class="text-red-500">*</span>
                        </label>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <!-- Stripe Elements will be inserted here -->
                            <div id="card-element" class="bg-white p-3 rounded border">
                                <!-- Stripe Elements Card will mount here -->
                            </div>
                            <div id="card-errors" role="alert" class="text-red-600 text-sm mt-2"></div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="error-message"
                        class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span id="error-text"></span>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div id="success-message"
                        class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="font-medium">Payment Successful!</p>
                                <p class="text-sm" id="success-details">Your payment has been processed successfully.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button id="submit-button" type="submit"
                            class="w-full bg-gradient-to-r from-stripe to-purple-600 hover:from-stripe-dark hover:to-purple-700 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-stripe focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-lg">
                            <span id="button-text" class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                                Send Payment Securely
                            </span>
                            <span id="loading-text" class="hidden flex items-center justify-center">
                                <svg class="animate-spin w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke-width="2"
                                        stroke-opacity="0.25" />
                                    <path stroke-linecap="round" stroke-width="2"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                                Processing Payment...
                            </span>
                        </button>
                    </div>

                    <!-- Security Notice -->
                    <div class="flex items-center justify-center text-xs text-gray-500 pt-2">
                        <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z" />
                        </svg>
                        Your payment is secured with 256-bit SSL encryption
                    </div>
                </form>
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-blue-50 rounded-lg p-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-blue-900 mb-1">Payment Information</h3>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• All payments are processed securely through Stripe</li>
                            <li>• You can cancel pending payments before they're completed</li>
                            <li>• Payment confirmations are sent via email</li>
                            <li>• Only active accounts with charges enabled are available</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize Stripe (replace with your publishable key)
            const stripe = Stripe('.....');
            const elements = stripe.elements();

            // Store accounts data
            let connectedAccounts = [];

            // Create card element
            const cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#424770',
                        '::placeholder': {
                            color: '#aab7c4',
                        },
                    },
                    invalid: {
                        color: '#9e2146',
                    },
                },
            });

            // Mount card element
            cardElement.mount('#card-element');

            // Handle real-time validation errors from the card Element
            cardElement.on('change', function(event) {
                const displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            // Set up CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('#csrf-token').val(),
                    'Accept': 'application/json'
                }
            });

            // Load connected accounts on page load
            loadConnectedAccounts();

            async function loadConnectedAccounts() {
                try {
                    showAccountsLoading(true);

                    const response = await $.ajax({
                        url: '/api/v1/stripe/connected-accounts',
                        method: 'GET',

                    });

                    console.log('Connected accounts loaded:', response);

                    connectedAccounts = response.data || [];
                    populateAccountsDropdown(connectedAccounts);

                    showAccountsLoading(false);

                } catch (error) {
                    console.error('Failed to load connected accounts:', error);
                    showAccountsLoading(false);

                    const dropdown = $('#fi_account_id');
                    dropdown.html('<option value="">Failed to load accounts</option>');

                    showError('Failed to load available accounts. Please refresh the page.');
                }
            }

            function populateAccountsDropdown(accounts) {
                const dropdown = $('#fi_account_id');
                dropdown.empty();

                if (accounts.length === 0) {
                    dropdown.html('<option value="">No active accounts available</option>');
                    dropdown.prop('disabled', true);
                    return;
                }

                dropdown.html('<option value="">Select Financial Institution Account</option>');

                accounts.forEach(account => {
                    const option = $('<option></option>')
                        .attr('value', account.id)
                        .text(`${account.display_name} - ${account.id}`)
                        .data('account', account);

                    dropdown.append(option);
                });

                dropdown.prop('disabled', false);
            }

            function showAccountsLoading(loading) {
                const dropdown = $('#fi_account_id');
                const loadingIndicator = $('#accounts-loading');

                if (loading) {
                    dropdown.prop('disabled', true);
                    loadingIndicator.removeClass('hidden');
                } else {
                    loadingIndicator.addClass('hidden');
                }
            }

            // Handle account selection change
            $('#fi_account_id').on('change', function() {
                const selectedValue = $(this).val();
                const accountInfo = $('#account-info');

                if (!selectedValue) {
                    accountInfo.addClass('hidden');
                    return;
                }

                // Find the selected account data
                const selectedAccount = connectedAccounts.find(acc => acc.id === selectedValue);

                if (selectedAccount) {
                    updateAccountInfo(selectedAccount);
                    accountInfo.removeClass('hidden');
                } else {
                    accountInfo.addClass('hidden');
                }
            });

            function updateAccountInfo(account) {
                $('#account-name').text(account.display_name || '-');
                $('#account-email').text(account.email || '-');
                $('#account-country').text(account.country || '-');
                $('#account-currency').text(account.default_currency?.toUpperCase() || '-');

                // Update status badge
                const statusElement = $('#account-status');
                const isActive = account.charges_enabled && account.payouts_enabled;

                if (isActive) {
                    statusElement
                        .removeClass('bg-red-100 text-red-800')
                        .addClass('bg-green-100 text-green-800')
                        .text('Active');
                } else {
                    statusElement
                        .removeClass('bg-green-100 text-green-800')
                        .addClass('bg-yellow-100 text-yellow-800')
                        .text('Limited');
                }
            }

            // Form submission handler
            $('#payment-form').on('submit', function(e) {
                e.preventDefault();

                // Clear previous messages
                hideMessages();

                // Validate form
                if (!validateForm()) {
                    return;
                }

                // Get form data
                const formData = getFormData();

                // Start loading state
                setLoadingState(true);

                // Process payment
                processPayment(formData);
            });

            function validateForm() {
                const amount = parseFloat($('#amount').val());
                const currency = $('#currency').val();
                const fiAccountId = $('#fi_account_id').val();

                if (!fiAccountId) {
                    showError('Please select a Financial Institution Account');
                    return false;
                }

                if (!amount || amount < 0.50) {
                    showError('Amount must be at least $0.50');
                    return false;
                }

                if (!currency) {
                    showError('Please select a currency');
                    return false;
                }

                return true;
            }

            function getFormData() {
                return {
                    fi_account_id: $('#fi_account_id').val(),
                    amount: Math.round(parseFloat($('#amount').val()) * 100), // Convert to cents
                    currency: $('#currency').val(),
                    loan_id: $('#loan_id').val(),
                    borrower_id: $('#borrower_id').val(),
                    description: $('#description').val() || null,
                    payment_method:"pm_card_visa",
                    borrower_name:$('#name').val(),
                     borrower_email:$('#email').val(),
                    confirm_now: true,
                    metadata: {
                        source: 'web_form',
                        timestamp: new Date().toISOString()
                    }
                };
            }

            async function processPayment(formData) {
                try {
                    // Step 1: Create PaymentIntent on server
                    const fiId = 1; // Replace with actual FI ID or get from form/context
                    const response = await $.ajax({
                        url: `/api/v1/fis/${fiId}/repayments`,
                        method: 'POST',
                        data: JSON.stringify(formData),
                        contentType: 'application/json'
                    });

                    if (!response.client_secret) {
                        throw new Error('No client secret received');
                    }

                    console.log('PaymentIntent created:', response.payment_intent);

                    // Step 2: Confirm payment with Stripe
                    // const result = await stripe.confirmCardPayment(response.client_secret, {
                    //     payment_method: {
                    //         card: cardElement,
                    //     }
                    // });

                    if (!response.id) {
                        // Payment failed
                        setLoadingState(false);
                        showError(response.error);
                    } else {
                        // Payment successful
                        console.log('Payment succeeded:', response.id);
                        setLoadingState(false);
                        showSuccess(
                            'Payment Successful!',
                            `Payment of ${formatAmount(formData.amount, formData.currency)} has been processed successfully.`
                        );

                        // Reset form after successful payment
                        setTimeout(() => {
                            resetForm();
                        }, 3000);
                    }

                } catch (error) {
                    console.error('Payment process failed:', error);
                    setLoadingState(false);

                    let errorMessage = 'Payment failed. Please try again.';
                    if (error.responseJSON && error.responseJSON.message) {
                        errorMessage = error.responseJSON.message;
                    } else if (error.message) {
                        errorMessage = error.message;
                    }

                    showError(errorMessage);
                }
            }

            function formatAmount(amountInCents, currency) {
                const amount = amountInCents / 100;
                const symbols = {
                    'usd': '$',
                    'eur': '€',
                    'gbp': '£'
                };
                return `${symbols[currency] || '$'}${amount.toFixed(2)}`;
            }

            function setLoadingState(loading) {
                const submitButton = $('#submit-button');
                const buttonText = $('#button-text');
                const loadingText = $('#loading-text');

                if (loading) {
                    submitButton.prop('disabled', true);
                    buttonText.addClass('hidden');
                    loadingText.removeClass('hidden');
                    cardElement.update({ disabled: true });
                } else {
                    submitButton.prop('disabled', false);
                    buttonText.removeClass('hidden');
                    loadingText.addClass('hidden');
                    cardElement.update({ disabled: false });
                }
            }

            function showError(message) {
                $('#error-text').text(message);
                $('#error-message').removeClass('hidden');

                // Auto hide after 5 seconds
                setTimeout(() => {
                    $('#error-message').addClass('hidden');
                }, 5000);
            }

            function showSuccess(title, details = '') {
                $('#success-message .font-medium').text(title);
                $('#success-details').text(details);
                $('#success-message').removeClass('hidden');
            }

            function hideMessages() {
                $('#error-message, #success-message').addClass('hidden');
                document.getElementById('card-errors').textContent = '';
            }

            function resetForm() {
                $('#payment-form')[0].reset();
                cardElement.clear();
                hideMessages();
                $('#account-info').addClass('hidden');
                // Reload accounts to refresh the dropdown
                loadConnectedAccounts();
            }

            // Real-time amount formatting
            $('#amount').on('blur', function() {
                if ($(this).val()) {
                    $(this).val(parseFloat($(this).val()).toFixed(2));
                }
            });

            // Currency change handler
            $('#currency').on('change', function() {
                const currency = $(this).val();
                const symbols = {
                    'usd': '$',
                    'eur': '€',
                    'gbp': '£'
                };

                $('.absolute.left-3').text(symbols[currency] || '$');
            });
        });
    </script>
</body>

</html>
