@extends('layouts.site')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/news.css') }}">
@endpush

@section('content')
    @include('site.partials.content.news')
@endsection

@push('scripts')
    <script src="{{ asset('js/site/news.js') }}"></script>
@endpush
