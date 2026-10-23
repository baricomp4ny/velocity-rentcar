<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    public function redirect()
    {
        if (! config('services.google.client_id')) {
            return redirect()->route('login')->with('error', 'Login dengan Google belum diaktifkan. Isi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET di file .env.');
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()->route('login')->with('error', 'Login dengan Google gagal, silakan coba lagi.');
        }

        $user = User::firstOrNew(['email' => $googleUser->getEmail()]);
        $user->fill([
            'name' => $user->name ?? ($googleUser->getName() ?: $googleUser->getEmail()),
            'google_id' => $googleUser->getId(),
            'avatar' => $user->avatar ?? $googleUser->getAvatar(),
        ])->save();

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended($user->homeUrl());
    }
}
