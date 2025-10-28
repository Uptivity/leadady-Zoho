<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (session()->get('crm_authenticated', false)) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt against env-backed credentials.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $expectedUsername = Config::get('crm.username');
        $expectedPassword = Config::get('crm.password');

        $usernameMatches = hash_equals((string) $expectedUsername, $credentials['username']);
        $passwordMatches = hash_equals((string) $expectedPassword, $credentials['password']);

        if (! ($usernameMatches && $passwordMatches)) {
            throw ValidationException::withMessages([
                'username' => __('These credentials do not match our records.'),
            ]);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->put('crm_authenticated', true);

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('crm_authenticated');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('crm.login.show');
    }
}
