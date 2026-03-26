<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::with('roles')
            ->orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group');

        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        $groups = Permission::distinct()->orderBy('group')->pluck('group');
        return view('admin.permissions.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100', 'regex:/^[a-z_]+$/', 'unique:permissions,name'],
            'display_name' => ['required', 'string', 'max:150'],
            'group'        => ['required', 'string', 'max:50'],
            'description'  => ['nullable', 'string', 'max:500'],
        ]);

        $permission = Permission::create($validated);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission \"{$permission->display_name}\" created.");
    }

    public function edit(Permission $permission)
    {
        $groups = Permission::distinct()->orderBy('group')->pluck('group');
        return view('admin.permissions.edit', compact('permission', 'groups'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:150'],
            'group'        => ['required', 'string', 'max:50'],
            'description'  => ['nullable', 'string', 'max:500'],
        ]);

        $permission->update($validated);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission \"{$permission->display_name}\" updated.");
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission \"{$permission->display_name}\" deleted.");
    }
}
