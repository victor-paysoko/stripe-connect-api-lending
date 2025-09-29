<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayConnect - Stripe Payment Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'stripe-blue': '#635BFF',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-2xl font-bold text-gray-900">PayConnect</h1>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="text-gray-500 hover:text-gray-700">Dashboard</button>
                        <button class="text-gray-500 hover:text-gray-700">Settings</button>
                        <button class="bg-stripe-blue text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Account
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Onboarding Section -->
            <div id="onboarding-section" class="mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="text-center">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-stripe-blue">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Connect your Stripe account</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Complete your account setup to start accepting payments from customers.
                            </p>
                        </div>

                        <!-- Account Status -->
                        <div class="mt-6">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Account Setup Required</h3>
                                        <p class="mt-1 text-sm text-yellow-700">
                                            Your account is not yet activated. Complete the onboarding process to start receiving payments.
                                        </p>
                                        <div class="mt-4">
                                            <button id="start-onboarding" class="bg-stripe-blue text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                                Complete Account Setup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Dashboard -->
            <div id="account-dashboard" class="hidden mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Account Status</h3>
                                <p class="mt-1 text-sm text-gray-500">Your Stripe Connect account details</p>
                            </div>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            </div>
                        </div>

                        <!-- Account Details -->
                        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                                <div class="p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                            </svg>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt class="text-sm font-medium text-gray-500 truncate">Balance</dt>
                                                <dd class="text-lg font-medium text-gray-900">$1,249.50</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                                <div class="p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt class="text-sm font-medium text-gray-500 truncate">Total Payments</dt>
                                                <dd class="text-lg font-medium text-gray-900">47</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                                <div class="p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                            </svg>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt class="text-sm font-medium text-gray-500 truncate">This Month</dt>
                                                <dd class="text-lg font-medium text-gray-900">$2,847.32</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex space-x-3">
                            <button id="view-dashboard" class="bg-stripe-blue text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                View Stripe Dashboard
                            </button>
                            <button class="bg-gray-200 text-gray-900 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">
                                Download Statement
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Service/Product Selection -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Select Service</h3>

                        <div class="space-y-4">
                            <div class="border rounded-lg p-4 cursor-pointer hover:border-stripe-blue transition-colors service-option" data-price="2999" data-service="Premium Consultation">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Premium Consultation</h4>
                                        <p class="text-sm text-gray-500">1-hour professional consultation</p>
                                    </div>
                                    <div class="text-lg font-semibold text-gray-900">$29.99</div>
                                </div>
                            </div>

                            <div class="border rounded-lg p-4 cursor-pointer hover:border-stripe-blue transition-colors service-option" data-price="4999" data-service="Business Review">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Business Review</h4>
                                        <p class="text-sm text-gray-500">Comprehensive business analysis</p>
                                    </div>
                                    <div class="text-lg font-semibold text-gray-900">$49.99</div>
                                </div>
                            </div>

                            <div class="border rounded-lg p-4 cursor-pointer hover:border-stripe-blue transition-colors service-option" data-price="9999" data-service="Full Package">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Full Package</h4>
                                        <p class="text-sm text-gray-500">Complete solution with follow-up</p>
                                    </div>
                                    <div class="text-lg font-semibold text-gray-900">$99.99</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>

                        <form id="payment-form">
                            <!-- Customer Information -->
                            <div class="mb-4">
                                <label for="customer-name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input type="text" id="customer-name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-stripe-blue focus:border-transparent" placeholder="John Doe" required>
                            </div>

                            <div class="mb-4">
                                <label for="customer-email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" id="customer-email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-stripe-blue focus:border-transparent" placeholder="john@example.com" required>
                            </div>

                            <!-- Stripe Elements -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Card Information</label>
                                <div id="card-element" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                                    <input type="text" placeholder="1234 5678 9012 3456" class="w-full bg-transparent outline-none mb-2">
                                    <div class="flex gap-2">
                                        <input type="text" placeholder="MM/YY" class="w-1/2 bg-transparent outline-none">
                                        <input type="text" placeholder="CVC" class="w-1/2 bg-transparent outline-none">
                                    </div>
                                </div>
                                <div id="card-errors" class="text-red-600 text-sm mt-2" role="alert"></div>
                            </div>

                            <!-- Order Summary -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <h4 class="font-medium text-gray-900 mb-2">Order Summary</h4>
                                <div class="flex justify-between items-center">
                                    <span id="selected-service" class="text-sm text-gray-600">Select a service above</span>
                                    <span id="selected-price" class="font-semibold text-gray-900">$0.00</span>
                                </div>
                                <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                                    <span class="text-sm text-gray-600">Platform fee (2.9% + $0.30)</span>
                                    <span id="platform-fee" class="text-sm text-gray-600">$0.30</span>
                                </div>
                                <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                                    <span class="font-medium text-gray-900">Total</span>
                                    <span id="total-price" class="font-semibold text-gray-900">$0.00</span>
                                </div>
                            </div>

                            <button type="submit" id="submit-payment" class="w-full bg-stripe-blue text-white py-3 px-4 rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                <span id="button-text">Select a service to continue</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="mt-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Transactions</h3>

                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Sarah Johnson</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Premium Consultation</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$29.99</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Mar 15, 2024</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Mike Chen</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Business Review</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$49.99</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Processing
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Mar 14, 2024</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Emma Wilson</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Full Package</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$99.99</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Mar 13, 2024</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedPrice = 0;
        let selectedService = '';

        // Handle onboarding button
        document.getElementById('start-onboarding').addEventListener('click', function() {
            document.getElementById('onboarding-section').classList.add('hidden');
            document.getElementById('account-dashboard').classList.remove('hidden');
        });

        // Handle service selection
        document.querySelectorAll('.service-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.service-option').forEach(opt => {
                    opt.classList.remove('border-stripe-blue', 'bg-blue-50');
                });

                this.classList.add('border-stripe-blue', 'bg-blue-50');

                selectedPrice = parseInt(this.dataset.price);
                selectedService = this.dataset.service;

                updateOrderSummary();
            });
        });

        function updateOrderSummary() {
            const priceInDollars = selectedPrice / 100;
            const platformFee = (selectedPrice * 0.029 + 30) / 100;
            const total = priceInDollars + platformFee;

            document.getElementById('selected-service').textContent = selectedService;
            document.getElementById('selected-price').textContent = `$${priceInDollars.toFixed(2)}`;
            document.getElementById('platform-fee').textContent = `$${platformFee.toFixed(2)}`;
            document.getElementById('total-price').textContent = `$${total.toFixed(2)}`;

            document.getElementById('submit-payment').disabled = false;
            document.getElementById('button-text').textContent = `Pay $${total.toFixed(2)}`;
        }

        // Handle form submission
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.getElementById('customer-name').value;
            const email = document.getElementById('customer-email').value;

            if (!name || !email || selectedPrice === 0) {
                document.getElementById('card-errors').textContent = 'Please fill in all fields and select a service.';
                return;
            }

            document.getElementById('button-text').textContent = 'Processing...';
            document.getElementById('submit-payment').disabled = true;

            // Simulate payment processing
            setTimeout(() => {
                alert('Payment processed successfully!');
                document.getElementById('button-text').textContent = 'Payment Successful!';
                document.getElementById('payment-form').reset();
                selectedPrice = 0;
                selectedService = '';
                document.querySelectorAll('.service-option').forEach(opt => {
                    opt.classList.remove('border-stripe-blue', 'bg-blue-50');
                });
                document.getElementById('selected-service').textContent = 'Select a service above';
                document.getElementById('selected-price').textContent = '$0.00';
                document.getElementById('platform-fee').textContent = '$0.30';
                document.getElementById('total-price').textContent = '$0.00';
                document.getElementById('button-text').textContent = 'Select a service to continue';
            }, 2000);
        });
    </script>
</body>
</html>
