@php
    $isEdit = isset($hotel);
    $action = $isEdit ? route('admin.hotels.update', $hotel) : route('admin.hotels.store');
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6 bg-white rounded-2xl shadow-sm p-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc pl-5 space-y-0.5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-bold text-summit mb-1">Hotel Name</label>
            <input type="text" name="name" value="{{ old('name', $hotel->name ?? '') }}" required class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm">
        </div>
        <div>
            <label class="block text-sm font-bold text-summit mb-1">Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $hotel->slug ?? '') }}" required class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm">
        </div>
    </div>

    <div>
        <label class="block text-sm font-bold text-summit mb-1">Booking URL</label>
        <input type="url" name="booking_url" value="{{ old('booking_url', $hotel->booking_url ?? '') }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm">
    </div>

    <div>
        <label class="block text-sm font-bold text-summit mb-1">Description</label>
        <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm">{{ old('description', $hotel->description ?? '') }}</textarea>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4">
            <h3 class="font-bold text-summit mb-3">Single Room Rate</h3>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">USD / night</label>
                    <input type="number" name="single_price_usd" value="{{ old('single_price_usd', $hotel->single_price_usd ?? 150) }}" required min="0" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">UGX / night</label>
                    <input type="number" name="single_price_ugx" value="{{ old('single_price_ugx', $hotel->single_price_ugx ?? 550000) }}" required min="0" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm bg-white">
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
            <h3 class="font-bold text-summit mb-3">Double Room Rate</h3>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">USD / night</label>
                    <input type="number" name="double_price_usd" value="{{ old('double_price_usd', $hotel->double_price_usd ?? 250) }}" required min="0" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">UGX / night</label>
                    <input type="number" name="double_price_ugx" value="{{ old('double_price_ugx', $hotel->double_price_ugx ?? 920000) }}" required min="0" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm bg-white">
                </div>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4 items-end">
        <div>
            <label class="block text-sm font-bold text-summit mb-1">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $hotel->sort_order ?? 0) }}" min="0" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm">
        </div>
        <label class="inline-flex items-center gap-2 text-sm font-semibold text-summit">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $hotel->is_active ?? true) ? 'checked' : '' }}>
            Active on public site
        </label>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('admin.hotels.index') }}" class="border border-gray-300 text-gray-700 font-bold py-3 px-5 rounded-xl">Cancel</a>
        <button type="submit"
                style="background:#1a2a4a;color:#fff;font-weight:700;padding:.75rem 1.5rem;border-radius:.75rem;border:none;cursor:pointer;"
                onmouseover="this.style.background='#0f1f3d'" onmouseout="this.style.background='#1a2a4a'">
            {{ $isEdit ? 'Update Hotel' : 'Create Hotel' }}
        </button>
    </div>
</form>
