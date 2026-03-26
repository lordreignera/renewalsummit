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
                    ['Affiliation',  $registration->affiliation === 'fcc' ? 'FCC Member' : 'Guest'],
                    ['Fee',          $registration->formattedTotal],
                    ['Accommodation Hotel', $registration->accommodationHotel->name ?? $registration->accommodation_choice ?? '—'],
                    ['Accommodation Mode', ucfirst(str_replace('_', ' ', $registration->accommodation_booking_mode ?? '—'))],
                    ['Room & Nights', ($registration->accommodation_room_type ? ucfirst($registration->accommodation_room_type) : '—') . ' · ' . ($registration->accommodation_nights ?: 1) . ' night(s)'],
                    ['Accommodation Total', $registration->accommodation_fee ? (($registration->accommodation_currency ?: $registration->currency) . ' ' . number_format($registration->accommodation_fee)) : '—'],
                    ['Accommodation Payment', ucfirst(str_replace('_', ' ', $registration->accommodation_payment_status ?? 'not_required'))],
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

        {{-- Emergency & Medical --}}
        @if($registration->emergency_contact_name || $registration->emergency_contact_phone || $registration->medical_conditions || $registration->allergies || $registration->mobility_needs || $registration->special_needs)
        <div class="bg-red-50 border border-red-200 rounded-2xl p-6 text-sm">
            <h3 class="font-bold text-red-800 mb-3">🚑 Emergency &amp; Medical</h3>
            <div class="grid grid-cols-2 gap-4">
                @if($registration->emergency_contact_name)
                <div>
                    <div class="text-xs text-red-400 font-medium">Emergency Contact Name</div>
                    <div class="font-semibold text-red-800 mt-0.5">{{ $registration->emergency_contact_name }}</div>
                </div>
                @endif
                @if($registration->emergency_contact_phone)
                <div>
                    <div class="text-xs text-red-400 font-medium">Emergency Contact Phone</div>
                    <div class="font-semibold text-red-800 mt-0.5">{{ $registration->emergency_contact_phone }}</div>
                </div>
                @endif
            </div>
            @foreach([
                ['Medical Conditions', $registration->medical_conditions],
                ['Food Allergies',     $registration->allergies],
                ['Mobility / Accessibility Needs', $registration->mobility_needs],
                ['Other Special Needs', $registration->special_needs],
            ] as [$label, $val])
                @if($val)
                <div class="mt-3">
                    <div class="text-xs text-red-400 font-medium">{{ $label }}</div>
                    <div class="font-semibold text-red-800 mt-0.5 whitespace-pre-line">{{ $val }}</div>
                </div>
                @endif
            @endforeach
        </div>
        @endif

        {{-- Payment History --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="font-bold text-summit text-lg mb-4">Payment History</h2>
            @php
                $registrationPayments = $registration->payments->where('payment_context', 'registration');
                $accommodationPayments = $registration->payments->where('payment_context', 'accommodation');
                $pc = ['success'=>'text-green-600','failed'=>'text-red-500','pending'=>'text-yellow-600'];
            @endphp

            <div class="mb-5">
                <h3 class="text-sm font-bold text-summit mb-3">Registration Payments</h3>
                @forelse($registrationPayments as $payment)
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
                        <div class="font-bold">{{ $payment->formattedAmount }}</div>
                        <div class="text-xs font-bold {{ $pc[$payment->status] ?? 'text-gray-400' }}">{{ strtoupper($payment->status) }}</div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">No registration payments yet.</p>
                @endforelse
            </div>

            <div>
                <h3 class="text-sm font-bold text-summit mb-3">Accommodation Payments</h3>
                @forelse($accommodationPayments as $payment)
                <div class="flex items-center gap-3 p-3 bg-teal-50 rounded-xl mb-2 text-sm border border-teal-100">
                    <div class="text-xl">{{ $payment->payment_method === 'visa' ? '💳' : '🏨' }}</div>
                    <div class="flex-1">
                        <div class="font-semibold">{{ strtoupper(str_replace('_', ' ', $payment->payment_method)) }}</div>
                        <div class="text-xs text-gray-400">
                            {{ $payment->network ?? '' }}
                            {{ $payment->phone_number ?? '' }}
                            {{ $payment->swapp_transaction_id ? '· TXN: ' . $payment->swapp_transaction_id : '' }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold">{{ $payment->formattedAmount }}</div>
                        <div class="text-xs font-bold {{ $pc[$payment->status] ?? 'text-gray-400' }}">{{ strtoupper($payment->status) }}</div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">No accommodation payments yet.</p>
                @endforelse
            </div>
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
            <img src="{{ route('qr.show', ['reference' => $registration->reference]) }}"
                 alt="QR" class="w-40 h-40 mx-auto border-4 border-yellow-400 rounded-xl">
            @if($registration->qr_sent_at)
            <p class="text-xs text-gray-400 mt-2">Sent: {{ $registration->qr_sent_at->format('d M Y H:i') }}</p>
            @endif
        </div>
        @endif

        {{-- Process Payment (unpaid / pending) --}}
        @if(!$registration->isPaid() && !$registration->isCheckedIn() && auth()->user()->hasRole('super_admin', 'registrar'))
        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-3">
            <h3 class="font-bold text-summit mb-2">Process Payment</h3>
            <p class="text-xs text-gray-500">Send a mobile money prompt or complete card payment on behalf of this attendee.</p>
            <a href="{{ route('admin.registrations.payment', $registration) }}"
               class="block text-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded-xl text-sm transition">
                💳 Send Payment Prompt
            </a>
        </div>
        @endif

        {{-- Actions --}}
        @if($registration->isPaid() || $registration->isCheckedIn())
        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-3">
            <h3 class="font-bold text-summit mb-2">Actions</h3>
            @if(auth()->user()->isSuperAdmin())
            <form method="POST" action="{{ route('admin.registrations.resend-qr', $registration) }}">
                @csrf
                <button class="w-full bg-gold hover:bg-yellow-600 text-white font-bold py-2 rounded-xl text-sm transition">
                    📧 Resend QR Email
                </button>
            </form>
            @endif
            <a href="{{ route('register.accommodation', ['reference' => $registration->reference, 'token' => $registration->qr_token]) }}"
               target="_blank"
               class="block text-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded-xl text-sm transition">
                🏨 Accommodation Planner
            </a>
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
