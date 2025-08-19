<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request, ensuring the authenticated user has any of the given roles.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // This middleware is used together with 'auth', so normally $user exists.
        if (!$user) {
            // Fallback: unauthenticated, treat as forbidden for JSON else redirect to login
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }
            return redirect()->guest(route('login'));
        }

        if (empty($roles)) {
            return $next($request);
        }

        if ($user->hasAnyRole($roles)) {
            return $next($request);
        }

        // Authenticated but lacks the required role.
        // For API/JSON requests, return 403. For web, redirect to user's own dashboard.
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        // Redirect to the appropriate dashboard based on the user's role.
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->hasRole('teacher')) {
                return redirect()->route('teachers.dashboard');
            }
            if ($user->hasRole('student')) {
                return redirect()->route('students.dashboard');
            }
            if ($user->hasRole('parent')) {
                return redirect()->route('parents.dashboard');
            }
            if ($user->hasRole('staff')) {
                return redirect()->route('staff.dashboard');
            }
        }

        // Fallback generic dashboard
        return redirect()->route('dashboard');
    }
}
