<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HotelController extends Controller
{
    public function index(): View
    {
        $hotels = Hotel::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.hotels.index', compact('hotels'));
    }

    public function create(): View
    {
        return view('admin.hotels.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateHotel($request);
        $data['is_active'] = $request->boolean('is_active');

        Hotel::create($data);

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel created successfully.');
    }

    public function edit(Hotel $hotel): View
    {
        return view('admin.hotels.edit', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel): RedirectResponse
    {
        $data = $this->validateHotel($request, $hotel);
        $data['is_active'] = $request->boolean('is_active');

        $hotel->update($data);

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel updated successfully.');
    }

    public function destroy(Hotel $hotel): RedirectResponse
    {
        $hotel->delete();

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel deleted successfully.');
    }

    private function validateHotel(Request $request, ?Hotel $hotel = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:191',
            'slug' => [
                'required',
                'string',
                'max:191',
                Rule::unique('hotels', 'slug')->ignore($hotel?->id),
            ],
            'description' => 'nullable|string|max:500',
            'image_path' => 'nullable|string|max:255',
            'booking_url' => 'nullable|url|max:500',
            'single_price_usd' => 'required|integer|min:0',
            'double_price_usd' => 'required|integer|min:0',
            'single_price_ugx' => 'required|integer|min:0',
            'double_price_ugx' => 'required|integer|min:0',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ]);
    }
}
