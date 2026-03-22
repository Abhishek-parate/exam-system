<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * ✅ FIX: Previously redirected ALL authenticated users to HOME ('/home')
     * which doesn't exist — causing an infinite redirect loop back to /login.
     * Now redirects each role to their correct dashboard.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // ✅ Role-aware redirect — each role goes to its own dashboard
                return redirect($this->getDashboardForUser($user));
            }
        }

        return $next($request);
    }

    /**
     * Return the correct dashboard URL based on the authenticated user's role.
     * Falls back to RouteServiceProvider::HOME if the role is unrecognised.
     */
    private function getDashboardForUser($user): string
    {
        // Guard against missing role relationship
        if (! $user || ! $user->role) {
            return RouteServiceProvider::HOME;
        }

        return match ($user->role->name) {
            'admin'   => '/admin/dashboard',
            'teacher' => '/teacher/dashboard',
            'student' => '/student/dashboard',
            'parent'  => '/parent/dashboard',
            default   => RouteServiceProvider::HOME,
        };
    }
}