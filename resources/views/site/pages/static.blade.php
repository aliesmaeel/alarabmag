@extends('layouts.site')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/chrome.css') }}">
    <style>
        .static-page { max-width: 780px; margin: 0 auto; padding: 3rem 2rem 5rem; font-family: 'Cairo', sans-serif; }
        .static-page h1 { font-family: 'Playfair Display', serif; font-size: 2.2rem; margin-bottom: 1rem; color: var(--ink, #1a1a1a); }
        .static-page .static-lead { font-size: 1.05rem; line-height: 1.9; color: var(--ink-soft, #555); margin-bottom: 2rem; }
        .static-page h2 { font-size: 1.25rem; margin: 2rem 0 .75rem; color: var(--ink, #1a1a1a); }
        .static-page p, .static-page li { font-size: .95rem; line-height: 1.95; color: var(--ink-soft, #444); margin-bottom: 1rem; }
        .static-page ul { padding-right: 1.25rem; margin-bottom: 1.5rem; }
        .static-page a { color: var(--gold, #c9a227); text-decoration: underline; }
        .static-page .static-meta { font-size: .85rem; color: #888; margin-bottom: 2rem; }
        .static-contact-box { background: #f8f4ee; padding: 1.5rem; border-radius: 4px; margin-top: 1.5rem; }
    </style>
@endpush

@section('content')
<main id="main" class="static-page">
    <div class="crumb" style="margin-bottom:2rem;font-size:.85rem;font-family:'Cairo',sans-serif;">
        <a href="{{ url('/') }}">الرئيسية</a>
        <span> › </span>
        <span>{{ $pageTitle }}</span>
    </div>
    <h1>{{ $pageTitle }}</h1>
    @if (!empty($pageLead))
        <p class="static-lead">{{ $pageLead }}</p>
    @endif
    @if (!empty($pageUpdated))
        <div class="static-meta">آخر تحديث: {{ $pageUpdated }}</div>
    @endif
    {!! $pageContent !!}
</main>
@endsection
