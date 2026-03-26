@extends('layouts.admin')
@section('title', 'Admin Users')
@section('page-title', 'Admin Users')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-sm text-gray-500 mt-1">Manage who has access to this admin panel and what they can do.</p>
    </div>
    <a href="{{ route('admin.users.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition">
        + Add User
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Name</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Email</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Role</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3 font-semibold text-gray-800">
                    {{ $user->name }}
                    @if($user->id === auth()->id())
                        <span class="ml-1 text-xs text-gray-400">(you)</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-gray-600">{{ $user->email }}</td>
                <td class="px-5 py-3">
                    @php
                        $roleColors = [
                            'super_admin' => 'bg-red-100 text-red-700',
                            'finance'     => 'bg-green-100 text-green-700',
                            'registrar'   => 'bg-blue-100 text-blue-700',
                        ];
                    @endphp
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ \App\Models\User::ROLES[$user->role] ?? $user->role }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-xs font-bold bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 rounded-lg transition">
                            Edit
                        </a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-xs font-bold bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg transition">
                                Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-5 py-10 text-center text-gray-400">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Role legend --}}
<div class="mt-6 bg-white rounded-2xl shadow-sm p-5">
    <h3 class="font-bold text-gray-700 mb-3 text-sm">Role Permissions</h3>
    <div class="grid md:grid-cols-3 gap-4 text-xs text-gray-600">
        <div class="border border-red-200 bg-red-50 rounded-xl p-4">
            <div class="font-bold text-red-700 text-sm mb-2">🔑 Super Admin</div>
            <ul class="space-y-1">
                <li>✅ Full dashboard &amp; all stats</li>
                <li>✅ View &amp; manage registrations</li>
                <li>✅ Manual registration + payment</li>
                <li>✅ Check-in scanner</li>
                <li>✅ Export CSV</li>
                <li>✅ Hotels &amp; room types</li>
                <li>✅ Video testimonials</li>
                <li>✅ User management</li>
            </ul>
        </div>
        <div class="border border-green-200 bg-green-50 rounded-xl p-4">
            <div class="font-bold text-green-700 text-sm mb-2">💰 Finance</div>
            <ul class="space-y-1">
                <li>✅ Full dashboard &amp; revenue stats</li>
                <li>✅ View all registrations</li>
                <li>✅ Export CSV</li>
                <li>✅ Check-in scanner</li>
                <li>❌ Hotels / testimonials</li>
                <li>❌ User management</li>
                <li>❌ Create registrations</li>
            </ul>
        </div>
        <div class="border border-blue-200 bg-blue-50 rounded-xl p-4">
            <div class="font-bold text-blue-700 text-sm mb-2">📲 Registrar</div>
            <ul class="space-y-1">
                <li>✅ Check-in scanner</li>
                <li>✅ View registrations list</li>
                <li>✅ View registration details</li>
                <li>❌ Revenue &amp; financial stats</li>
                <li>❌ Export CSV</li>
                <li>❌ Hotels / testimonials</li>
                <li>❌ User management</li>
            </ul>
        </div>
    </div>
</div>

@endsection
