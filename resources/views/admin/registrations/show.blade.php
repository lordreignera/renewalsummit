@extends('layouts.admin')
@section('title', $registration->full_name)
@section('page-title', $registration->reference)

@section('content')

<div class="grid md:grid-cols-3 gap-6">

    {{-- ── Left: Profile & Details ──────────────────────────────── --}}
    <div class="md:col-span-2 space-y-5">

        {{-- Personal Info --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="font-bold text-summit text-lg mb-4">Personal Details</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                @php
                $designationLabels = [
                    'fcc_regional_leader' => 'FCC Regional Leader',
                    'senior_pastor'       => 'Senior Pastor',
                    'church_leader'       => 'Church Leader' . ($registration->designation_specify ? " – {$registration->designation_specify}" : ''),
                    'corporate'           => 'Corporate / Organisation',
                ];
                @endphp
                @foreach([
                    ['Full Name',    $registration->full_name],
                    ['Designation',  $designationLabels[$registration->designation] ?? $registration->designation],
                    ['Phone',        $registration->phone],
                    ['Email',        $registration->email ?? '—'],
                    ['Address',      $registration->address ?? '—'],
                    ['Attendee Type', $registration->country_type === 'local' ? 'Ugandan' : ($registration->country_type === 'africa' ? 'Rest of Africa' : 'International')],
                    ['Nationality',  $registration->nationality ?? '—'],
                    ['Affiliation',  $registration->affiliation === 'fcc' ? 'FCC Member' : 'Other / Guest'],
                    ['Fee',          $registration->formattedTotal],
                ] as [$label, $val])
                <div>
                    <div class="text-xs text-gray-400 font-medium">{{ $label }}</div>
                    <div class="font-semibold text-summit mt-0.5">{{ $val }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- FCC Info --}}
        @if($registration->isFcc())
        <div class="bg-purple-50 border border-purple-200 rounded-2xl p-6 text-sm">
            <h3 class="font-bold text-purple-800 mb-3">✝️ FCC Details</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-purple-400">FCC Region</div>
                    <div class="font-semibold text-purple-800">{{ $registration->fcc_region ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-purple-400">FCC Regional Leader</div>
                    <div class="font-semibold text-purple-800">{{ $registration->fcc_regional_leader ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-purple-400">Church</div>
                    <div class="font-semibold text-purple-800">{{ $registration->fcc_church ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-purple-400">Local Pastor</div>
                    <div class="font-semibold text-purple-800">{{ $registration->fcc_pastor ?? '—' }}</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Payment History --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="font-bold text-summit text-lg mb-4">Payment History</h2>
            @forelse($registration->payments as $payment)
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl mb-2 text-sm">
                <div class="text-xl">{{ $payment->payment_method === 'visa' ? '💳' : '📱' }}</div>
                <div class="flex-1">
                    <div class="font-semibold">{{ strtoupper(str_replace('_', ' ', $payment->payment_method)) }}</div>
                    <div class="text-xs text-gray-400">
                        {{ $payment->network ?? '' }}
                        {{ $payment->phone_number ?? '' }}
                        {{ $payment->swapp_transaction_id ? '· TXN: ' . $payment->swapp_transaction_id : '' }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-bold">UGX {{ number_format($payment->amount) }}</div>
                    @php $pc = ['success'=>'text-green-600','failed'=>'text-red-500','pending'=>'text-yellow-600']; @endphp
                    <div class="text-xs font-bold {{ $pc[$payment->status] ?? 'text-gray-400' }}">
                        {{ strtoupper($payment->status) }}
                    </div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400">No payment records yet.</p>
            @endforelse
        </div>

    </div>

    {{-- ── Right: Status & QR ────────────────────────────────────── --}}
    <div class="space-y-5">

        {{-- Status Card --}}
        <div class="bg-summit text-white rounded-2xl p-6">
            <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Reference</div>
            <div class="text-2xl font-extrabold text-yellow-400 mb-4">{{ $registration->reference }}</div>

            @php $colors = [
                'draft'      => 'bg-gray-600',
                'pending'    => 'bg-yellow-500',
                'paid'       => 'bg-green-600',
                'checked_in' => 'bg-blue-500',
                'cancelled'  => 'bg-red-600',
            ]; @endphp

            <span class="inline-block {{ $colors[$registration->status] ?? 'bg-gray-600' }} text-white
                         text-sm font-bold px-4 py-1.5 rounded-full mb-4">
                {{ strtoupper(str_replace('_', ' ', $registration->status)) }}
            </span>

            <div class="text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-400">Currency</span>
                    <span>{{ $registration->currency }}</span>
                </div>
                <div class="flex justify-between border-t border-white/20 pt-2">
                    <span class="font-bold">Fee Paid</span>
                    <span class="font-extrabold text-yellow-400">{{ $registration->formattedTotal }}</span>
                </div>
            </div>

            @if($registration->checked_in_at)
            <div class="mt-4 text-xs text-blue-300">
                ✅ Checked in: {{ $registration->checked_in_at->format('D d M Y H:i') }}
            </div>
            @endif
        </div>

        {{-- QR Code --}}
        @if($registration->qr_code_path)
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
            <h3 class="font-bold text-summit mb-3">QR Code</h3>
            <img src="{{ Storage::disk('public')->url($registration->qr_code_path) }}"
                 alt="QR" class="w-40 h-40 mx-auto border-4 border-yellow-400 rounded-xl">
            @if($registration->qr_sent_at)
            <p class="text-xs text-gray-400 mt-2">Sent: {{ $registration->qr_sent_at->format('d M Y H:i') }}</p>
            @endif
        </div>
        @endif

        {{-- Actions --}}
        @if($registration->isPaid() || $registration->isCheckedIn())
        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-3">
            <h3 class="font-bold text-summit mb-2">Actions</h3>
            <form method="POST" action="{{ route('admin.registrations.resend-qr', $registration) }}">
                @csrf
                <button class="w-full bg-gold hover:bg-yellow-600 text-white font-bold py-2 rounded-xl text-sm transition">
                    📧 Resend QR Email
                </button>
            </form>
            <a href="{{ route('admin.checkin.process', $registration->qr_token) }}"
               class="block text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-xl text-sm transition">
                📲 Manual Check-In
            </a>
        </div>
        @endif
    </div>

</div>

<div class="mt-4">
    <a href="{{ route('admin.registrations.index') }}"
       class="text-sm text-gold hover:underline">← Back to all registrations</a>
</div>

@endsection
