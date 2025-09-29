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
                    {{-- <div class="flex items-center space-x-4">
                        <button class="text-gray-500 hover:text-gray-700">Dashboard</button>
                        <button class="text-gray-500 hover:text-gray-700">Settings</button>
                        <button class="bg-stripe-blue text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Account
                        </button>
                    </div> --}}
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
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
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
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Account Setup Required</h3>
                                        <p class="mt-1 text-sm text-yellow-700">
                                            Your account is not yet activated. Complete the onboarding process to start
                                            receiving payments.
                                        </p>
                                        <div class="mt-4">
                                            <button id="start-onboarding"
                                                class="bg-stripe-blue text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
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
            {{-- <div id="account-dashboard" class="hidden mb-8">
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
            </div> --}}


  <div class="mt-8">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Connected FI Stripe Accounts</h3>
                <button onclick="refreshAccounts()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Refresh
                </button>
            </div>

            <div id="accounts-container">
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Currency</th>
                                {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Charges</th> --}}
                                {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payouts</th> --}}
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="accounts-tbody">
                            <!-- Accounts will be populated here -->
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-4" id="accounts-cards">
                    <!-- Account cards will be populated here -->
                </div>
            </div>

            <!-- Loading State -->
            <div id="loading-state" class="hidden text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                <p class="mt-2 text-sm text-gray-500">Loading accounts...</p>
            </div>

            <!-- Error State -->
            <div id="error-state" class="hidden mt-4 p-4 bg-red-50 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error loading account</h3>
                        <div class="mt-2 text-sm text-red-700" id="error-message"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal for Account Details -->
<div id="account-modal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Account Capabilities</h3>
                <div id="modal-content" class="text-sm text-gray-500"></div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button onclick="closeModal()" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>





            <div class="mt-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Customers </h3>

                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Customer</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Service</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Sarah
                                            Johnson</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Premium
                                            Consultation</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$29.99</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Mar 15, 2024</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Mike
                                            Chen</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Business Review
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$49.99</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Processing
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Mar 14, 2024</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Emma
                                            Wilson</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Full Package</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$99.99</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
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
function formatDate(timestamp) {
    const date = new Date(timestamp * 1000);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function getStatusBadge(enabled) {
    if (enabled) {
        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Enabled</span>';
    }
    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Disabled</span>';
}

async function fetchAllAccounts() {
    try {
        const response = await fetch(`/api/v1/stripe/connected-accounts`);
        console.log(response);

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Failed to fetch account');
        }
        console.log(data);

        return data;
    } catch (error) {
        console.log(error);
        console.error('Error fetching account:', error);
        throw error;
    }
}

function showAccountCapabilities(capabilities) {
    const capabilitiesHtml = Object.entries(capabilities).map(([key, value]) => {
        const status = value === 'active' ? 'text-green-600' : 'text-yellow-600';
        return `
            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="font-medium">${key.replace(/_/g, ' ').toUpperCase()}</span>
                <span class="${status}">${value}</span>
            </div>
        `;
    }).join('');

    document.getElementById('modal-content').innerHTML = capabilitiesHtml;
    document.getElementById('account-modal').classList.remove('hidden');
}

async function viewAccountDetails(accountId) {
    try {
        // Show loading in modal
        document.getElementById('modal-content').innerHTML = '<div class="text-center py-4">Loading...</div>';
        document.getElementById('account-modal').classList.remove('hidden');

        const response = await fetch(`/api/v1/stripe/connected-accounts/${accountId}`);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Failed to fetch account details');
        }

        // Display capabilities if available
        if (data.capabilities && Object.keys(data.capabilities).length > 0) {
            showAccountCapabilities(data.capabilities);
        } else {
            document.getElementById('modal-content').innerHTML = '<div class="text-center py-4 text-gray-500">No capabilities information available</div>';
        }
    } catch (error) {
        document.getElementById('modal-content').innerHTML = `<div class="text-center py-4 text-red-500">Error: ${error.message}</div>`;
    }
}

function closeModal() {
    document.getElementById('account-modal').classList.add('hidden');
}

function renderAccountRow(account) {
    return `
        <tr>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${account.display_name}</div>
                <div class="text-xs text-gray-500">${account.id}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${account.email || 'N/A'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${account.country}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${account.default_currency.toUpperCase()}</td>

            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatDate(account.created)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <button onclick='viewAccountDetails("${account.id}")' class="text-indigo-600 hover:text-indigo-900">View Details</button>
            </td>
        </tr>
    `;
}

function showError(message) {
    document.getElementById('error-message').textContent = message;
    document.getElementById('error-state').classList.remove('hidden');
}

function hideError() {
    document.getElementById('error-state').classList.add('hidden');
}

async function loadAllAccounts() {
    const tbody = document.getElementById('accounts-tbody');
    const loading = document.getElementById('loading-state');
    const container = document.getElementById('accounts-container');

    tbody.innerHTML = '';
    hideError();
    container.classList.add('hidden');
    loading.classList.remove('hidden');

    try {
        const response = await fetchAllAccounts();

        // Extract accounts from the response
        const accountsArray = response.data || [];
        const totalCount = response.count || 0;

        // Update title with count
        const titleElement = document.querySelector('h3');
        if (titleElement && totalCount > 0) {
            titleElement.textContent = `Connected Stripe Accounts (${totalCount})`;
        }

        if (accountsArray.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                        No connected accounts found.
                    </td>
                </tr>
            `;
        } else {
            accountsArray.forEach(account => {
                tbody.innerHTML += renderAccountRow(account);
            });
        }
    } catch (error) {
        showError(error.message);
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-sm text-red-500">
                    Failed to load accounts. Please try again.
                </td>
            </tr>
        `;
    } finally {
        loading.classList.add('hidden');
        container.classList.remove('hidden');
    }
}

function refreshAccounts() {
    loadAllAccounts();
}

// Load accounts on page load
document.addEventListener('DOMContentLoaded', () => {
    loadAllAccounts();
});
</script>
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
                document.getElementById('card-errors').textContent =
                    'Please fill in all fields and select a service.';
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
