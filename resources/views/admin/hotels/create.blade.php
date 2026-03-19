@extends('layouts.admin')
@section('title', 'Create Hotel')
@section('page-title', 'Create Hotel')

@section('content')
<div class="space-y-4">
    <div>
        <h2 class="text-xl font-extrabold text-summit">Add New Hotel</h2>
        <p class="text-sm text-gray-500 mt-1">Create a hotel option for public accommodation planning and admin logistics tracking.</p>
    </div>

    @include('admin.hotels._form')
</div>
@endsection
