@extends('layouts.app')

@section('content')
<section class="bg-white py-16 sm:py-24"><div class="mx-auto max-w-6xl px-5 sm:px-8">
    <p class="text-sm font-bold uppercase tracking-widest text-indigo-600">Career Guide</p><h1 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-950">Practical resources for your next move.</h1>
    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">@foreach (['CV tips' => 'Present your experience clearly and tailor it to the role.', 'Interview preparation' => 'Prepare examples that show how you think and work.', 'Job search tips' => 'Build a thoughtful, consistent application routine.', 'Career growth' => 'Choose learning opportunities that support your goals.', 'Workplace skills' => 'Strengthen the habits that make teams work well.', 'Professional development' => 'Keep learning visible and connected to your ambitions.'] as $title => $description)<article class="rounded-2xl border border-slate-200 p-6 shadow-sm"><h2 class="text-lg font-bold text-slate-900">{{ $title }}</h2><p class="mt-2 leading-6 text-slate-600">{{ $description }}</p></article>@endforeach</div>
</div></section>
@endsection
