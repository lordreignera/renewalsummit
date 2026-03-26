<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions')
            ->orderBy('name')
            ->get()
            ->map(function (Role $role) {
                $role->user_count = User::where('role', $role->name)->count();
                return $role;
            });

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('group')->orderBy('display_name')->get()->groupBy('group');
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:50', 'regex:/^[a-z_]+$/', 'unique:roles,name'],
            'display_name'   => ['required', 'string', 'max:100'],
            'description'    => ['nullable', 'string', 'max:500'],
            'permissions'    => ['nullable', 'array'],
            'permissions.*'  => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::create([
            'name'         => $validated['name'],
            'display_name' => $validated['display_name'],
            'description'  => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->display_name}\" created.");
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')->orderBy('display_name')->get()->groupBy('group');
        $assigned    = $role->permissions->pluck('id')->all();
        return view('admin.roles.edit', compact('role', 'permissions', 'assigned'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'display_name'   => ['required', 'string', 'max:100'],
            'description'    => ['nullable', 'string', 'max:500'],
            'permissions'    => ['nullable', 'array'],
            'permissions.*'  => ['integer', 'exists:permissions,id'],
        ]);

        $role->update([
            'display_name' => $validated['display_name'],
            'description'  => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->display_name}\" updated.");
    }

    public function destroy(Role $role)
    {
        $userCount = User::where('role', $role->name)->count();

        if ($userCount > 0) {
            return back()->with('error', "Cannot delete \"{$role->display_name}\": {$userCount} user(s) still have this role.");
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->display_name}\" deleted.");
    }
}
