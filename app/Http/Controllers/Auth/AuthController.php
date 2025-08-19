<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    private array $allowedTypes = ['admin', 'teacher', 'student', 'parent', 'staff'];

    public function showLoginForm(?string $type = null)
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->to($this->dashboardRouteFor(\Illuminate\Support\Facades\Auth::user()));
        }
        $type = $type && in_array($type, $this->allowedTypes) ? $type : null;
        return view('auth.login', compact('type'));
    }

    public function login(Request $request, ?string $type = null)
    {
        $type = $type && in_array($type, $this->allowedTypes) ? $type : null;

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = (bool) ($credentials['remember'] ?? false);
        unset($credentials['remember']);

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = $request->user();
            // Only approved and active users can log in
            if ($user->status !== 'approved' || !$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is not approved or is inactive. Please contact the administrator.',
                ])->onlyInput('email');
            }

            // If logging in via role-specific route, ensure user has that role
            if ($type && !$user->hasRole($type)) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'This login is for ' . ucfirst($type) . ' accounts. Your account does not have this role.',
                ])->onlyInput('email');
            }

            return redirect()->intended($this->dashboardRouteFor($user));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm(?string $type = null)
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->to($this->dashboardRouteFor(\Illuminate\Support\Facades\Auth::user()));
        }
        $type = $type && in_array($type, $this->allowedTypes) ? $type : null;
        return view('auth.register', compact('type'));
    }

    public function register(Request $request, ?string $type = null)
    {
        $type = $type && in_array($type, $this->allowedTypes) ? $type : null;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => 'pending',
            'is_active' => false,
            'pending_role' => $type,
        ]);

        // Do not auto-login; admin must approve
        return redirect()->route('login')->with('status', 'Registration submitted. An administrator will approve your account.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function dashboardRouteFor(User $user): string
    {
        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        }
        if ($user->hasRole('teacher')) {
            return route('teachers.dashboard');
        }
        if ($user->hasRole('student')) {
            return route('students.dashboard');
        }
        if ($user->hasRole('parent')) {
            return route('parents.dashboard');
        }
        if ($user->hasRole('staff')) {
            return route('staff.dashboard');
        }
        return route('dashboard');
    }
}
