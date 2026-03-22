<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($credentials, $request->filled('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // ✅ FIX: Eagerly load the role relationship right after login.
        //    Without this, $user->role is null at this point because Laravel's
        //    Auth::user() returns a fresh model without eager-loaded relations.
        //    Calling $user->isAdmin() then hits $this->role->name on a null
        //    object — Laravel catches the fatal and silently redirects back to
        //    /login, producing the "login loop" with no visible error.
        $user->load('role');

        // ✅ Guard: if role is still missing log it and show a friendly error
        if (! $user->role) {
            Log::warning("User ID {$user->id} ({$user->email}) has no role assigned.");
            Auth::logout();
            $request->session()->invalidate();
            return back()->withErrors([
                'email' => 'Your account has no role assigned. Please contact the administrator.',
            ])->onlyInput('email');
        }

        // Redirect based on role
        if ($user->isAdmin()) {
            return redirect()->intended('/admin/dashboard');
        }

        if ($user->isTeacher()) {
            return redirect()->intended('/teacher/dashboard');
        }

        if ($user->isStudent()) {
            return redirect()->intended('/student/dashboard');
        }

        if ($user->isParent()) {
            return redirect()->intended('/parent/dashboard');
        }

        // Unknown role — log and redirect safely
        Log::warning("User ID {$user->id} has unknown role: {$user->role->name}");
        Auth::logout();
        $request->session()->invalidate();
        return back()->withErrors([
            'email' => 'Your account role is not recognised. Please contact the administrator.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}