@extends('layouts.site')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/blog-details.css') }}">
@endpush

@section('content')
    @include('site.partials.content.blog-details')
@endsection

@push('scripts')
    <script>window.SITE_ARTICLE_ID = {{ $articleId }};</script>
    <script src="{{ asset('js/site/blog-details.js') }}"></script>
@endpush
