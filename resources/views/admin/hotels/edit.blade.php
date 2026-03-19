@extends('layouts.admin')
@section('title', 'Edit Hotel')
@section('page-title', 'Edit Hotel')

@section('content')
<div class="space-y-4">
    <div>
        <h2 class="text-xl font-extrabold text-summit">Edit Hotel</h2>
        <p class="text-sm text-gray-500 mt-1">Update rates, imagery, booking links, and visibility.</p>
    </div>

    @include('admin.hotels._form', ['hotel' => $hotel])
</div>
@endsection
