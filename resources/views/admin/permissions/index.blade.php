@extends('layouts.admin')
@section('title', 'Permissions')
@section('page-title', 'Permissions')

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Define individual permission gates that can be assigned to roles.</p>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.roles.index') }}"
           class="text-sm text-gray-600 hover:text-gray-900 font-medium">← Roles</a>
        <a href="{{ route('admin.permissions.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition">
            + Add Permission
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
    {{ session('error') }}
</div>
@endif

@forelse($permissions as $group => $perms)
<div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-5">
    <div class="bg-gray-50 border-b border-gray-200 px-5 py-3">
        <h3 class="font-bold text-gray-700 text-sm">{{ $group }}</h3>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100">
                <th class="px-5 py-2.5 text-left font-semibold text-gray-500 text-xs">Slug</th>
                <th class="px-5 py-2.5 text-left font-semibold text-gray-500 text-xs">Display Name</th>
                <th class="px-5 py-2.5 text-left font-semibold text-gray-500 text-xs">Description</th>
                <th class="px-5 py-2.5 text-left font-semibold text-gray-500 text-xs">Roles</th>
                <th class="px-5 py-2.5 text-left font-semibold text-gray-500 text-xs">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($perms as $permission)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3">
                    <code class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $permission->name }}</code>
                </td>
                <td class="px-5 py-3 font-medium text-gray-800">{{ $permission->display_name }}</td>
                <td class="px-5 py-3 text-gray-500 text-xs max-w-xs">{{ $permission->description ?? '—' }}</td>
                <td class="px-5 py-3">
                    <div class="flex flex-wrap gap-1">
                        @forelse($permission->roles as $role)
                            <span class="text-xs bg-purple-100 text-purple-700 font-semibold px-2 py-0.5 rounded-full">
                                {{ $role->display_name }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-400">—</span>
                        @endforelse
                    </div>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.permissions.edit', $permission) }}"
                           class="text-xs font-bold bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 rounded-lg transition">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}"
                              onsubmit="return confirm('Delete permission \'{{ $permission->name }}\'?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-xs font-bold bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@empty
<div class="bg-white rounded-2xl shadow-sm p-10 text-center text-gray-400">
    No permissions defined yet.
</div>
@endforelse

@endsection
