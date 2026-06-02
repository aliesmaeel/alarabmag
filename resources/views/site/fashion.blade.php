@extends('layouts.site')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/news.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site/fashion.css') }}">
@endpush

@section('content')
    @include('site.partials.content.fashion')
@endsection

@push('scripts')
    <script src="{{ asset('js/site/fashion.js') }}"></script>
@endpush
