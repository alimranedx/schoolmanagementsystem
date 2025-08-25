<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $status = $request->query('status');

        // If the request expects JSON (e.g., Admin Dashboard fetch), return a single paginator of users
        if ($request->expectsJson()) {
            $usersJson = User::with('roles')
                ->when($q, function($query) use ($q){
                    $query->where(function($sub) use ($q){
                        $sub->where('name', 'like', "%$q%")
                            ->orWhere('email', 'like', "%$q%");
                    });
                })
                ->when($status, fn($query) => $query->where('status', $status))
                ->orderBy('name')
                ->paginate(15)
                ->appends(['q' => $q, 'status' => $status]);
            return response()->json($usersJson);
        }

        // Separate paginators for pending and all users (HTML view)
        $pendingUsers = User::with('roles')
            ->when($q, function($query) use ($q){
                $query->where(function($sub) use ($q){
                    $sub->where('name', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%");
                });
            })
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'pending_page')
            ->appends(['q' => $q, 'status' => $status]);
        // All users (optionally filter by status)
        $users = User::with('roles')
            ->when($q, function($query) use ($q){
                $query->where(function($sub) use ($q){
                    $sub->where('name', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%");
                });
            })
            ->when($status, fn($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(15, ['*'], 'users_page')
            ->appends(['q' => $q, 'status' => $status]);
        $mode = 'both';
        return view('admin.users.index', compact('pendingUsers','users','q','status','mode'));
    }

    public function pending(Request $request)
    {
        $q = $request->query('q');
        // Only pending users
        $pendingUsers = User::with('roles')
            ->when($q, function($query) use ($q){
                $query->where(function($sub) use ($q){
                    $sub->where('name', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%");
                });
            })
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'pending_page')
            ->appends(['q' => $q]);
        // Do not load full users list here
        $users = collect();
        $status = 'pending';
        $mode = 'pending';
        return view('admin.users.index', compact('pendingUsers','users','q','status','mode'));
    }

    public function all(Request $request)
    {
        $q = $request->query('q');
        $status = $request->query('status');
        // All users (optionally filter by status)
        $users = User::with('roles')
            ->when($q, function($query) use ($q){
                $query->where(function($sub) use ($q){
                    $sub->where('name', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%");
                });
            })
            ->when($status, fn($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(15, ['*'], 'users_page')
            ->appends(['q' => $q, 'status' => $status]);
        // Do not load pending list here
        $pendingUsers = collect();
        $mode = 'all';
        return view('admin.users.index', compact('pendingUsers','users','q','status','mode'));
    }

    public function assignRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $role = Role::where('name', $data['role'])->firstOrFail();
        $user->roles()->syncWithoutDetaching([$role->id]);

        return response()->json($user->load('roles'));
    }

    public function revokeRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $role = Role::where('name', $data['role'])->firstOrFail();
        $user->roles()->detach($role->id);

        return response()->json($user->load('roles'));
    }

    // Approve a pending user and optionally assign pending_role as actual role
    public function approve(User $user)
    {
        $user->status = 'approved';
        $user->is_active = true;
        $user->save();

        if ($user->pending_role) {
            if ($role = Role::where('name', $user->pending_role)->first()) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }
            $user->pending_role = null;
            $user->save();
        }

        if (request()->expectsJson()) {
            return response()->json(['status' => 'approved', 'user' => $user->load('roles')]);
        }
        return back()->with('success', 'User approved successfully.');
    }

    public function activate(User $user)
    {
        $user->is_active = true;
        $user->save();
        if (request()->expectsJson()) {
            return response()->json(['status' => 'activated']);
        }
        return back()->with('success', 'User activated.');
    }

    public function deactivate(User $user)
    {
        $user->is_active = false;
        $user->save();
        if (request()->expectsJson()) {
            return response()->json(['status' => 'deactivated']);
        }
        return back()->with('success', 'User deactivated.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'status' => 'sometimes|in:pending,approved',
            'is_active' => 'sometimes|boolean',
        ]);
        $user->update($data);
        if ($request->expectsJson()) {
            return response()->json($user->fresh('roles'));
        }
        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        if (request()->expectsJson()) {
            return response()->json(['status' => 'deleted']);
        }
        return back()->with('success', 'User deleted successfully.');
    }
}
