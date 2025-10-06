<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        try {
            return view('auth.login');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading the login page: ' . $e->getMessage());
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();

            $request->session()->regenerate();

            // Redirect based on user role
            $user = $request->user();
            if ($user->can('admin')) {
                return redirect()->intended(route('admin.dashboard', absolute: false));
            } else {
                return redirect()->intended(route('employee.leaves.index', absolute: false));
            }
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->with('error', 'Login failed: ' . $e->getMessage());
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return redirect('/');
        } catch (Exception $e) {
            return redirect('/')->with('error', 'An error occurred during logout: ' . $e->getMessage());
        }
    }
}
