<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        $users = User::with('roles')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'super_admin');
            })
            ->orderBy('name')
            ->paginate(20);
        
        return view('users.index', compact('users'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        $roles = Role::where('name', '!=', 'super_admin')->get();
        
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'login' => 'nullable|string|max:255|unique:users|regex:/^[a-zA-Z0-9_-]+$/',
            'password' => ['required', 'confirmed', Password::min(8)],
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'login' => $validated['login'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => $request->has('is_active'),
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        $user->syncRoles($validated['roles']);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Accès non autorisé');
        }

        $user->load('roles');
        
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->hasRole('super_admin')) {
            abort(403, 'Vous ne pouvez pas modifier un Super Admin.');
        }

        $roles = Role::where('name', '!=', 'super_admin')->get();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->hasRole('super_admin')) {
            abort(403, 'Vous ne pouvez pas modifier un Super Admin.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'login' => 'nullable|string|max:255|unique:users,login,' . $user->id . '|regex:/^[a-zA-Z0-9_-]+$/',
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
            'is_active' => 'boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'login' => $validated['login'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $user->syncRoles($validated['roles']);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur modifié avec succès.');
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->hasRole('super_admin')) {
            abort(403, 'Vous ne pouvez pas supprimer un Super Admin.');
        }

        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function toggleStatus(User $user)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->hasRole('super_admin')) {
            abort(403, 'Vous ne pouvez pas modifier un Super Admin.');
        }

        // Empêcher la désactivation de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'activé' : 'désactivé';

        return redirect()->route('users.index')
            ->with('success', "Utilisateur {$status} avec succès.");
    }
}
