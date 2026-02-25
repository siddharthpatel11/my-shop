<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    private array $providers = ['google', 'facebook'];

    /**
     * Redirect to the provider's OAuth page.
     */
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        // TEMPORARY DEBUG - remove after fixing
        // dd(config('services.' . $provider . '.redirect'));

        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Handle the provider callback.
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('customer.login')
                ->with('error', 'Social login failed: ' . $e->getMessage());
        }

        $customer = $this->findOrCreateCustomer($socialUser, $provider);

        if (!$customer || $customer->status !== 'active') {
            return redirect()->route('customer.login')
                ->with('error', 'Your account is inactive. Please contact support.');
        }

        Auth::guard('customer')->login($customer, true);

        session()->put([
            'customer_id'    => $customer->id,
            'customer_name'  => $customer->name,
            'customer_email' => $customer->email,
            'login_time'     => now(),
        ]);

        return redirect()->route('frontend.products.index')
            ->with('success', 'Welcome, ' . $customer->name . '!');
    }

    /**
     * Find an existing customer or create a new one from social data.
     */
    private function findOrCreateCustomer($socialUser, string $provider): Customer
    {
        $providerIdField = $provider . '_id'; // google_id / facebook_id

        // 1. Returning user — find by provider ID
        $customer = Customer::where($providerIdField, $socialUser->getId())->first();
        if ($customer) {
            $customer->update(['avatar' => $socialUser->getAvatar()]);
            return $customer;
        }

        // 2. Existing email/password account — link social to it
        if ($socialUser->getEmail()) {
            $customer = Customer::where('email', $socialUser->getEmail())->first();
            if ($customer) {
                $customer->update([
                    $providerIdField  => $socialUser->getId(),
                    'avatar'          => $socialUser->getAvatar(),
                    'social_provider' => $provider,
                ]);
                return $customer;
            }
        }

        // 3. Brand-new customer
        return Customer::create([
            'name'            => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Customer',
            'email'           => $socialUser->getEmail()
                ?? $provider . '_' . $socialUser->getId() . '@social.local',
            'password'        => null,
            'phone_number'    => null,
            'status'          => 'active',
            $providerIdField  => $socialUser->getId(),
            'avatar'          => $socialUser->getAvatar(),
            'social_provider' => $provider,
        ]);
    }

    /**
     * Validate that the provider is supported.
     */
    private function validateProvider(string $provider): void
    {
        if (!in_array($provider, $this->providers)) {
            abort(404, 'Social provider not supported.');
        }
    }
}
