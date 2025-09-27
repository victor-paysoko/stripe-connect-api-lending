<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Make Payment - {{ config('app.name') }}</title>
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

                    <!-- Financial Institution ID -->
                    {{-- <div>
                        <label for="fi_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Financial Institution <span class="text-red-500">*</span>
                        </label>
                        <select id="fi_id" name="fi_id" required
                            class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-stripe focus:border-transparent transition-colors bg-white">
                            <option value="">Select Financial Institution...</option>
                            <option value="1">Bank of Example</option>
                            <option value="2">Credit Union Plus</option>
                            <option value="3">Community Bank</option>
                        </select>
                    </div> --}}

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
                            {{-- <p class="mt-1 text-xs text-gray-500">Minimum: $0.50</p> --}}
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

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description (Optional)
                        </label>
                        <input type="text" id="description" name="description" maxlength="255"
                            class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-stripe focus:border-transparent transition-colors"
                            placeholder="What is this payment for?">
                    </div>

                    <!-- Card Details -->
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <select required name="payment_method" id="payment_method"
                            class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-stripe focus:border-transparent transition-colors bg-white">
                            <option value="pm_card_visa">Visa Card</option>
                            <option value="pm_card_mastercard">Mastercard</option>
                            <option value="pm_card_amex">American Express</option>
                        </select>
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
                                <p class="text-sm" id="success-details">Your payment has been processed successfully.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button id="submit-button"
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
                            <li>• Platform fees are optional and set by the service provider</li>
                            <li>• Payment confirmations are sent via email</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Set up CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('#csrf-token').val(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

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

                // Create payment intent first
                createPaymentIntent(formData);
            });

            // FI selection change handler - check health
            $('#fi_id').on('change', function() {
                const fiId = $(this).val();
                if (fiId) {
                    checkFiHealth(fiId);
                }
            });

            function validateForm() {
                const fiId = $('#fi_id').val();
                const amount = parseFloat($('#amount').val());
                const currency = $('#currency').val();

                // if (!fiId) {
                //     showError('Please select a Financial Institution');
                //     return false;
                // }

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
                    // fi_id: parseInt($('#fi_id').val()),
                    fi_account_id: 'acct_1SBgcUGrErqSqkFH',
                    amount: Math.round(parseFloat($('#amount').val()) * 100),
                    currency: $('#currency').val(),
                    loan_id: $('#loan_id').val(),
                    borrower_id: $('#borrower_id').val(),
                    description: $('#description').val() || null,
                    payment_method: $('#payment_method').val(),
                    metadata: {
                        source: 'web_form',
                        timestamp: new Date().toISOString()
                    }
                };
            }

            console.log(formData);



            function createPaymentIntent(formData) {
                console.log(formData);

                $.ajax({
                    url: `/api/v1/fis/${formData.fi_id}/repayments/payment-intent`,
                    method: 'POST',
                    data: JSON.stringify(formData),
                    success: function(response) {
                        console.log('Payment Intent created:', response.client_secret);

                        if (response.client_secret) {
                            // Store payment intent ID for retrieval


                            const paymentIntentId = response.payment_id;

                            // Simulate payment processing (in real scenario, you'd use Stripe.js here)
                            // setTimeout(() => {
                            //     processPayment(paymentIntentId);
                            // }, 2000);
                              setLoadingState(false);
                            showSuccess('payment created');

                        } else {
                            setLoadingState(false);
                            showError('Failed to create payment intent');
                        }
                    },
                    error: function(xhr) {
                        setLoadingState(false);
                        handleAjaxError(xhr, 'Failed to create payment intent');
                    }
                });
            }



            function setLoadingState(loading) {
                const submitButton = $('#submit-button');
                const buttonText = $('#button-text');
                const loadingText = $('#loading-text');

                if (loading) {
                    submitButton.prop('disabled', true);
                    buttonText.addClass('hidden');
                    loadingText.removeClass('hidden');
                } else {
                    submitButton.prop('disabled', false);
                    buttonText.removeClass('hidden');
                    loadingText.addClass('hidden');
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

            function showAlert(type, message, autoHide = 0) {
                const alertClass = {
                    'success': 'bg-green-50 border-green-200 text-green-700',
                    'error': 'bg-red-50 border-red-200 text-red-700',
                    'warning': 'bg-yellow-50 border-yellow-200 text-yellow-700',
                    'info': 'bg-blue-50 border-blue-200 text-blue-700'
                };

                const iconColor = {
                    'success': 'text-green-500',
                    'error': 'text-red-500',
                    'warning': 'text-yellow-500',
                    'info': 'text-blue-500'
                };

                const alertHtml = `
                <div class="mb-6 ${alertClass[type]} px-4 py-3 rounded-lg flex items-center alert-message">
                    <svg class="w-5 h-5 mr-2 ${iconColor[type]}" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    ${message}
                </div>
            `;

                $('#alert-container').prepend(alertHtml);

                if (autoHide > 0) {
                    setTimeout(() => {
                        $('.alert-message').first().fadeOut(() => {
                            $(this).remove();
                        });
                    }, autoHide);
                }
            }

            function hideMessages() {
                $('#error-message, #success-message').addClass('hidden');
                $('.alert-message').remove();
            }

            function handleAjaxError(xhr, defaultMessage) {
                console.error('AJAX Error:', xhr);

                let errorMessage = defaultMessage;

                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    } else if (response.error) {
                        errorMessage = response.error;
                    } else if (response.errors) {
                        // Handle validation errors
                        const errors = Object.values(response.errors).flat();
                        errorMessage = errors.join(', ');
                    }
                } catch (e) {
                    // Use default message if parsing fails
                }

                showError(errorMessage);
            }

            function resetForm() {
                $('#payment-form')[0].reset();
                hideMessages();
            }

            // Real-time amount formatting
            $('#amount').on('input', function() {
                let value = $(this).val();
                if (value && !isNaN(value)) {
                    // Format to 2 decimal places on blur
                    $(this).on('blur', function() {
                        if ($(this).val()) {
                            $(this).val(parseFloat($(this).val()).toFixed(2));
                        }
                    });
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
