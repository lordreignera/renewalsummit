@extends('layouts.admin')
@section('title', 'Create Role')
@section('page-title', 'Create Role')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.roles.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Back to Roles</a>
</div>

<form method="POST" action="{{ route('admin.roles.store') }}">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Role Details --}}
        <div class="lg:col-span-1 space-y-5">
            <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
                <h2 class="font-bold text-gray-700">Role Details</h2>

                {{-- Slug --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Role Slug <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="e.g. finance"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror"
                           pattern="[a-z_]+" title="Lowercase letters and underscores only">
                    <p class="text-xs text-gray-400 mt-1">Lowercase letters and underscores only. Cannot be changed after creation.</p>
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Display Name --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Display Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="display_name" value="{{ old('display_name') }}"
                           placeholder="e.g. Finance Team"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('display_name') border-red-400 @enderror">
                    @error('display_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              placeholder="What can this role do?"
                              class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl transition text-sm">
                    Create Role
                </button>
            </div>
        </div>

        {{-- Permissions --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="font-bold text-gray-700 mb-4">Assign Permissions</h2>

                @forelse($permissions as $group => $perms)
                <div class="mb-5">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 pb-1 border-b">
                        {{ $group }}
                    </h3>
                    <div class="space-y-2">
                        @foreach($perms as $perm)
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                   {{ in_array($perm->id, old('permissions', [])) ? 'checked' : '' }}
                                   class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span>
                                <span class="text-sm font-medium text-gray-800 group-hover:text-blue-600">{{ $perm->display_name }}</span>
                                @if($perm->description)
                                <span class="block text-xs text-gray-400">{{ $perm->description }}</span>
                                @endif
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">No permissions defined yet.
                    <a href="{{ route('admin.permissions.create') }}" class="text-blue-600 hover:underline">Create permissions first →</a>
                </p>
                @endforelse
            </div>
        </div>

    </div>
</form>

@endsection
