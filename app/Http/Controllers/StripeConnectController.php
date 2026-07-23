<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;

class StripeConnectController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Redirect manufacturer to Stripe onboarding.
     */
    public function connect()
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== 'manufacturer') {
            abort(403, 'Only manufacturers can link Stripe accounts.');
        }

        // 1. Create a connected Express account if they don't have one
        if (empty($user->stripe_connect_id)) {
            try {
                $account = Account::create([
                    'type' => 'express',
                    'email' => $user->email,
                    'capabilities' => [
                        'card_payments' => ['requested' => true],
                        'transfers' => ['requested' => true],
                    ],
                ]);
                
                $user->update([
                    'stripe_connect_id' => $account->id,
                    'stripe_onboarding_completed' => false,
                ]);
            } catch (\Exception $e) {
                return back()->with('error', 'Stripe connection initiation failed: ' . $e->getMessage());
            }
        }

        // 2. Generate an onboarding Link
        try {
            $accountLink = AccountLink::create([
                'account' => $user->stripe_connect_id,
                'refresh_url' => route('manufacturer.stripe.connect'),
                'return_url' => route('manufacturer.stripe.callback'),
                'type' => 'account_onboarding',
            ]);

            return redirect()->away($accountLink->url);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate onboarding link: ' . $e->getMessage());
        }
    }

    /**
     * Callback after onboarding completes or gets cancelled.
     */
    public function callback()
    {
        /** @var User $user */
        $user = auth()->user();

        if (empty($user->stripe_connect_id)) {
            return redirect()->route('manufacturer.payment-methods')->with('error', 'Stripe account ID missing.');
        }

        try {
            // Retrieve account details from Stripe to verify onboarding is completed
            $account = Account::retrieve($user->stripe_connect_id);

            if ($account->details_submitted) {
                $user->update([
                    'stripe_onboarding_completed' => true,
                ]);
                return redirect()->route('manufacturer.payment-methods')->with('success', 'Stripe account connected successfully!');
            }

            return redirect()->route('manufacturer.payment-methods')->with('warning', 'Stripe onboarding was not fully completed. Please complete all fields.');
        } catch (\Exception $e) {
            return redirect()->route('manufacturer.payment-methods')->with('error', 'Failed to verify Stripe connection: ' . $e->getMessage());
        }
    }

    /**
     * Remove Stripe connection.
     */
    public function disconnect()
    {
        /** @var User $user */
        $user = auth()->user();

        $user->update([
            'stripe_connect_id' => null,
            'stripe_onboarding_completed' => false,
        ]);

        return back()->with('success', 'Stripe connection removed.');
    }
}
