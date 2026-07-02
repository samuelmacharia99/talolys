<?php

namespace App\Http\Controllers\Platform\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('platform.auth.login', ['pageTitle' => 'Platform Login']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('platform')->attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'status'   => true,
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('platform.dashboard'));
        }

        return back()->withErrors(['username' => 'Invalid credentials.'])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::guard('platform')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('platform.login');
    }
}
