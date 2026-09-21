@extends('layouts.app')

@section('content')
<article class="bg-white py-16 sm:py-24"><div class="mx-auto max-w-3xl px-5 sm:px-8"><a href="{{ route('blog.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">← Back to blog</a><p class="mt-8 text-sm font-bold uppercase tracking-widest text-indigo-600">{{ $article['category'] }}</p><h1 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-950">{{ $article['title'] }}</h1><p class="mt-4 text-sm text-slate-500">{{ $article['date'] }}</p><div class="mt-10 text-lg leading-8 text-slate-700"><p>{{ $article['body'] }}</p></div></div></article>
@endsection
