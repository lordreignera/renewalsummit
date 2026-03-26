@extends('layouts.admin')
@section('title', 'Edit Permission')
@section('page-title', 'Edit Permission')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.permissions.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Back to Permissions</a>
</div>

<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.permissions.update', $permission) }}">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
            <h2 class="font-bold text-gray-700">Edit Permission</h2>

            {{-- Slug (read-only) --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Permission Slug</label>
                <div class="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm text-gray-500">
                    {{ $permission->name }}
                </div>
                <p class="text-xs text-gray-400 mt-1">Slug cannot be changed after creation.</p>
            </div>

            {{-- Display Name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Display Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="display_name" value="{{ old('display_name', $permission->display_name) }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('display_name') border-red-400 @enderror">
                @error('display_name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Group --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Group <span class="text-red-500">*</span>
                </label>
                <input type="text" name="group" value="{{ old('group', $permission->group) }}"
                       list="group-suggestions"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('group') border-red-400 @enderror">
                <datalist id="group-suggestions">
                    @foreach($groups as $g)
                    <option value="{{ $g }}">
                    @endforeach
                </datalist>
                @error('group')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2"
                          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $permission->description) }}</textarea>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl transition text-sm">
                Save Changes
            </button>
        </div>
    </form>
</div>

@endsection
