@extends('layouts.site')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/news-details.css') }}">
@endpush

@section('content')
    @include('site.partials.content.news-details')
@endsection

@push('scripts')
    <script>window.SITE_ARTICLE_ID = {{ $articleId }};</script>
    <script src="{{ asset('js/site/news-details.js') }}"></script>
@endpush
