@extends('layouts.public')
@section('title', 'Payment Pending | Renewal Summit 2026')

@section('content')
<div class="max-w-lg mx-auto px-4 py-16 text-center">

    <div class="bg-white rounded-2xl shadow-lg p-10">
        <div class="text-6xl mb-6 animate-pulse">⏳</div>
        <h1 class="text-2xl font-extrabold text-summit mb-2">Awaiting Payment</h1>
        <p class="text-gray-500 mb-6">
            A payment prompt has been sent to your phone. Please check your phone and
            <strong>approve the Mobile Money request</strong> to complete your registration.
        </p>

        <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-4 mb-6 text-sm text-yellow-800">
            <div class="font-bold mb-1">📱 What to do:</div>
            <ol class="list-decimal ml-4 space-y-1 text-left">
                <li>Check your phone for a payment notification</li>
                <li>Enter your Mobile Money PIN to approve</li>
                <li>Wait for this page to refresh automatically</li>
            </ol>
        </div>

        <div class="text-sm text-gray-500 mb-6">
            <div>Reference: <span class="font-bold text-summit">{{ $reg->reference }}</span></div>
            <div>Amount: <span class="font-bold text-gold">UGX {{ number_format($reg->total_amount) }}</span></div>
        </div>

        {{-- Status indicator --}}
        <div id="status-indicator" class="mb-6">
            <div class="flex items-center justify-center gap-2 text-yellow-600 text-sm font-medium">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                Checking payment status...
            </div>
        </div>

        <a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-gray-600">← Return to Home</a>
    </div>
</div>

@push('scripts')
<script>
    const reference = '{{ $reg->reference }}';
    const statusUrl = '{{ route('payment.status', $reg->reference) }}';
    const completeUrl = '{{ route('register.complete') }}?ref=' + reference;

    let attempts = 0;
    const maxAttempts = 40; // ~2 minutes

    function checkStatus() {
        if (attempts >= maxAttempts) {
            document.getElementById('status-indicator').innerHTML =
                '<p class="text-red-500 text-sm">Payment confirmation timed out. Please <a href="' + window.location.href + '" class="underline">refresh</a> or contact support if you were charged.</p>';
            return;
        }
        attempts++;

        fetch(statusUrl)
            .then(r => r.json())
            .then(data => {
                if (data.paid) {
                    window.location.href = completeUrl;
                } else {
                    setTimeout(checkStatus, 3000);
                }
            })
            .catch(function() { setTimeout(checkStatus, 5000); });
    }

    setTimeout(checkStatus, 3000);
</script>
@endpush
@endsection
