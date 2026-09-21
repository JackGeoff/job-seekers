@extends('layouts.auth')

@section('content')
    <div class="animate-fade-in">
        <div class="mb-8 sm:mb-10">
            <p class="mb-3 text-sm font-semibold text-indigo-600">EMPLOYER ACCOUNT</p>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">Start recruiting with Jobseekers.</h1>
            <p class="mt-3 text-base leading-7 text-slate-600">Create an employer account to choose a plan and set up your company profile.</p>
        </div>

        <form method="POST" action="{{ route('employer.register.store') }}" class="space-y-5">
            @csrf
            @foreach (['name' => 'Contact person name', 'email' => 'Work email address', 'phone' => 'Phone number'] as $field => $label)
                <div>
                    <label for="{{ $field }}" class="mb-2 block text-sm font-medium text-slate-800">{{ $label }}</label>
                    <input type="{{ $field === 'email' ? 'email' : ($field === 'phone' ? 'tel' : 'text') }}" id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" required @if($field === 'phone') inputmode="numeric" pattern="[0-9]+" @endif autocomplete="{{ $field === 'name' ? 'name' : $field }}"
                        class="block h-14 w-full rounded-xl border bg-white px-4 text-base text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 @error($field) border-red-500 @else border-slate-200 @enderror">
                    @error($field)<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            @endforeach
            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-slate-800">Password</label>
                <input type="password" id="password" name="password" required autocomplete="new-password" class="block h-14 w-full rounded-xl border border-slate-200 bg-white px-4 text-base text-slate-950 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
                @error('password')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-800">Confirm password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" class="block h-14 w-full rounded-xl border border-slate-200 bg-white px-4 text-base text-slate-950 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
            </div>
            <button type="submit" class="flex h-14 w-full items-center justify-center rounded-xl bg-indigo-600 px-5 text-base font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/25">Create employer account</button>
        </form>
        <p class="mt-8 text-center text-sm text-slate-600">Already have an account? <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-800">Log in</a></p>
    </div>
@endsection
