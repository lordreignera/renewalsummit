@extends('layouts.admin')
@section('title', 'Roles')
@section('page-title', 'Roles')

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Define roles and assign permissions to control what each role can do.</p>
    <a href="{{ route('admin.roles.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition">
        + Add Role
    </a>
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

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Role</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Slug</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Description</th>
                <th class="px-5 py-3 text-center font-semibold text-gray-600">Permissions</th>
                <th class="px-5 py-3 text-center font-semibold text-gray-600">Users</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($roles as $role)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3 font-semibold text-gray-800">{{ $role->display_name }}</td>
                <td class="px-5 py-3">
                    <code class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $role->name }}</code>
                </td>
                <td class="px-5 py-3 text-gray-500 text-xs max-w-xs truncate">
                    {{ $role->description ?? '—' }}
                </td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs font-bold bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">
                        {{ $role->permissions_count }}
                    </span>
                </td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                        {{ $role->user_count }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.roles.edit', $role) }}"
                           class="text-xs font-bold bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 rounded-lg transition">
                            Edit
                        </a>
                        @if($role->user_count === 0)
                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                              onsubmit="return confirm('Delete role \'{{ $role->display_name }}\'? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-xs font-bold bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg transition">
                                Delete
                            </button>
                        </form>
                        @else
                        <span class="text-xs text-gray-400 italic">In use</span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-10 text-center text-gray-400">No roles defined yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4 text-right">
    <a href="{{ route('admin.permissions.index') }}"
       class="text-sm text-blue-600 hover:underline">
        → Manage Permissions
    </a>
</div>

@endsection
