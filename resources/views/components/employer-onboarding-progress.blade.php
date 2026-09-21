@php
    $steps = [
        ['label' => 'Choose a plan', 'route' => 'employer.pricing'],
        ['label' => 'Payment', 'route' => 'employer.payment'],
        ['label' => 'Company profile', 'route' => 'employer.profile'],
        ['label' => 'Start hiring', 'route' => 'employer.dashboard'],
    ];
@endphp

<nav class="mx-auto mb-8 max-w-3xl" aria-label="Employer onboarding progress">
    <ol class="grid grid-cols-4 gap-2 sm:gap-4">
        @foreach ($steps as $index => $step)
            @php($stepNumber = $index + 1)
            <li class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $currentStep >= $stepNumber ? 'bg-brand-700 text-white' : 'border border-slate-300 bg-white text-slate-500' }}" aria-current="{{ $currentStep === $stepNumber ? 'step' : 'false' }}">{{ $stepNumber }}</span>
                    @if ($index < count($steps) - 1)
                        <span class="hidden h-px flex-1 sm:block {{ $currentStep > $stepNumber ? 'bg-brand-300' : 'bg-slate-200' }}"></span>
                    @endif
                </div>
                <span class="mt-2 block truncate text-[11px] font-semibold {{ $currentStep === $stepNumber ? 'text-brand-800' : 'text-slate-500' }} sm:text-xs">{{ $step['label'] }}</span>
            </li>
        @endforeach
    </ol>
</nav>
