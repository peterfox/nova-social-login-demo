<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Nova\Nova;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class SocialLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            // Displays the G-Suite domain saving the user from typing their whole
            // email address.
            ->with(['hd' => 'ylsideas.co'])
            ->redirect();
    }

    public function processGoogleCallback(Request $request)
    {
        try {
            $socialUser = Socialite::driver('google')->user();
        } catch (InvalidStateException $exception) {
            return redirect()->route('nova.login')
                ->withErrors([
                    'email' => [
                        __('Google Login failed, please try again.'),
                    ],
                ]);
        }

        // Very Important! Stops anyone with any google accessing Nova!
        if (! Str::endsWith($socialUser->getEmail(), 'ylsideas.co')) {
            return redirect()->route('nova.login')
                ->withErrors([
                    'email' => [
                        __('Only ylsideas.co email addresses are accepted.'),
                    ],
                ]);
        }

        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName(),
                'password' => Str::random(32),
            ]
        );

        $this->guard()->login($user);

        return redirect()->intended(Nova::path());
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard(config('nova.guard'));
    }
}
