<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function start()
    {
        return Auth::check() ? redirect(Auth::user()->homeUrl()) : view('auth.start');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [], ['login' => 'Email atau Nomor Telepon']);

        // Pengguna boleh masuk memakai email ATAU nomor telepon.
        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (! Auth::attempt([$field => $data['login'], 'password' => $data['password']], $request->boolean('remember'))) {
            throw ValidationException::withMessages(['login' => 'Email/Nomor Telepon atau password salah.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(Auth::user()->homeUrl());
    }

    /**
     * Masuk langsung ke akun demo (hanya jika DEMO_LOGIN=true).
     */
    public function demoLogin(Request $request, string $role)
    {
        $email = config("services.demo_login.accounts.{$role}");
        abort_unless(config('services.demo_login.enabled') && $email, 404);

        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()->with('error', 'Akun demo belum ada. Jalankan: php artisan migrate:fresh --seed');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($user->homeUrl());
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create($request->safe()->except('terms'));

        $user->sendNotification(
            'Selamat datang di '.setting('store_name'),
            'Akun kamu berhasil dibuat. Yuk cari mobil yang cocok untuk perjalananmu!',
            route('home')
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('start');
    }
}
