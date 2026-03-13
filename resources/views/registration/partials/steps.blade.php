{{-- Step progress indicator. $currentStep = 1-3 --}}
@php $steps = ['Personal Info', 'Affiliation', 'Payment']; @endphp
<div class="flex items-center justify-between mb-8">
    @foreach($steps as $i => $label)
        @php $num = $i + 1; $active = $num === $currentStep; $done = $num < $currentStep; @endphp
        <div class="flex-1 flex flex-col items-center relative">
            {{-- Connector line --}}
            @if($i > 0)
                <div class="absolute left-0 top-4 h-0.5 w-full -z-10
                            {{ $done || $active ? 'bg-yellow-400' : 'bg-gray-200' }}"></div>
            @endif
            {{-- Circle --}}
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold z-10
                        {{ $done ? 'bg-green-500 text-white' : ($active ? 'bg-gold text-white' : 'bg-gray-200 text-gray-500') }}">
                {{ $done ? '✓' : $num }}
            </div>
            <span class="text-xs mt-1 font-medium {{ $active ? 'text-yellow-600' : ($done ? 'text-green-600' : 'text-gray-400') }}">
                {{ $label }}
            </span>
        </div>
    @endforeach
</div>
