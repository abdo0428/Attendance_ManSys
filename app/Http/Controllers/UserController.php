<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user->syncRoles([$validated['role']]);

        AuditLog::record('user.role.updated', $user, ['role' => $validated['role']]);

        return response()->json(['ok' => true]);
    }
}

