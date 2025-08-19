<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(20);
        return response()->json($users);
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

        return response()->json(['status' => 'approved', 'user' => $user->load('roles')]);
    }

    public function activate(User $user)
    {
        $user->is_active = true;
        $user->save();
        return response()->json(['status' => 'activated']);
    }

    public function deactivate(User $user)
    {
        $user->is_active = false;
        $user->save();
        return response()->json(['status' => 'deactivated']);
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
        return response()->json($user->fresh('roles'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['status' => 'deleted']);
    }
}
