@extends('layouts.admin')
@section('title', 'Registration Payment')
@section('page-title', 'Step 3: Payment')

@section('content')
<div class="space-y-4">
    <div class="bg-white rounded-2xl shadow-sm p-4 md:p-5">
        <h2 class="text-lg font-extrabold text-summit">Complete Registration Fee Payment</h2>
        <p class="text-sm text-gray-500 mt-1">
            Attendee: <strong>{{ $registration->full_name }}</strong>
            <span class="mx-1">·</span>
            Ref: <strong>{{ $registration->reference }}</strong>
        </p>
        <p class="text-xs text-gray-400 mt-2">
            This embeds the same payment step used on the public flow.
        </p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="height: calc(100vh - 260px); min-height: 680px;">
        <iframe
            id="embedded-payment-frame"
            title="Registration Payment"
            src="{{ route('register.step3', ['embed' => 1]) }}"
            class="w-full h-full border-0"
            loading="lazy"
        ></iframe>
    </div>

    <div>
        <a href="{{ route('admin.registrations.show', $registration) }}" class="text-sm text-gold hover:underline">
            ← Back to Registration Details
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const frame = document.getElementById('embedded-payment-frame');
        if (!frame) {
            return;
        }

        function hideEmbeddedNavbar() {
            try {
                const doc = frame.contentDocument || frame.contentWindow.document;
                const nav = doc.querySelector('nav');
                if (nav) {
                    nav.style.display = 'none';
                }
            } catch (e) {
                // Same-origin expected; ignore if frame content is temporarily unavailable.
            }
        }

        frame.addEventListener('load', hideEmbeddedNavbar);
    })();
</script>
@endpush
