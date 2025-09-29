<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\StripeConnectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OnboardingController extends Controller
{
    protected StripeConnectService $stripeService;

    public function __construct(StripeConnectService $stripeService)
    {
        $this->stripeService = $stripeService;

    }

    /**
     * Show the onboarding dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $this->stripeService->updateAccountStatus($user);

        return view('onboarding.index', compact('user'));
    }

    /**
     * Start Stripe Connect onboarding
     */
    public function startStripeOnboarding(): RedirectResponse
    {
        $user = Auth::user();

        // Create Stripe account if doesn't exist
        if (!$user->hasStripeAccount()) {
            $accountId = $this->stripeService->createConnectAccount($user);
            if (!$accountId) {
                return redirect()->back()->with('error', 'Failed to create Stripe account. Please try again.');
            }
        }

        // Create onboarding link
        $returnUrl = route('onboarding.stripe.return');
        $refreshUrl = route('onboarding.stripe.refresh');

        $onboardingUrl = $this->stripeService->createAccountLink($user, $returnUrl, $refreshUrl);

        if (!$onboardingUrl) {
            return redirect()->back()->with('error', 'Failed to create onboarding link. Please try again.');
        }

        return redirect($onboardingUrl);
    }

    /**
     * Handle return from Stripe onboarding
     */
    public function stripeReturn(): RedirectResponse
    {
        $user = Auth::user();
        $this->stripeService->updateAccountStatus($user);

        if ($user->isStripeOnboardingComplete()) {
            return redirect()->route('onboarding.index')
                ->with('success', 'Congratulations! Your Stripe account has been successfully set up.');
        }

        return redirect()->route('onboarding.index')
            ->with('warning', 'Your Stripe account setup is not complete. Please continue the onboarding process.');
    }

    /**
     * Handle refresh from Stripe onboarding
     */
    public function stripeRefresh(): RedirectResponse
    {
        $user = Auth::user();

        $returnUrl = route('onboarding.stripe.return');
        $refreshUrl = route('onboarding.stripe.refresh');

        $onboardingUrl = $this->stripeService->createAccountLink($user, $returnUrl, $refreshUrl);

        if (!$onboardingUrl) {
            return redirect()->route('onboarding.index')
                ->with('error', 'Failed to refresh onboarding link. Please try again.');
        }

        return redirect($onboardingUrl);
    }

    /**
     * Open Stripe Dashboard
     */
    public function stripeDashboard(): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->canAcceptPayments()) {
            return redirect()->route('onboarding.index')
                ->with('error', 'Please complete your Stripe onboarding first.');
        }

        $dashboardUrl = $this->stripeService->createDashboardLink($user);

        if (!$dashboardUrl) {
            return redirect()->back()->with('error', 'Failed to access Stripe dashboard. Please try again.');
        }

        return redirect($dashboardUrl);
    }

    /**
     * Complete onboarding process
     */
    public function complete()
    {
        $user = Auth::user();

        if (!$user->canAcceptPayments()) {
           return redirect()->route('onboarding.index')->with('error','complete all steps first');
        }

        return view('onboarding.complete', compact('user'));
    }
}
