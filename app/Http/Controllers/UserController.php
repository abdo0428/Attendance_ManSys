<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()?->company_id;
        $users = User::with('roles')
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();
        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_id' => $request->user()->company_id,
        ]);

        $user->assignRole($validated['role']);

        AuditLog::record('user.created', $user, ['role' => $validated['role']]);

        return redirect()
            ->route('users.index')
            ->with('toast', ['type' => 'success', 'message' => __('app.toast_saved')]);
    }

    public function updateRole(Request $request, User $user)
    {
        if ($user->company_id !== $request->user()->company_id) {
            abort(404);
        }

        $validated = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user->syncRoles([$validated['role']]);

        AuditLog::record('user.role.updated', $user, ['role' => $validated['role']]);

        return response()->json(['ok' => true]);
    }
}
