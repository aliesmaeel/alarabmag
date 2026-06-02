@extends('layouts.site')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/artists.css') }}">
@endpush

@section('content')
    @include('site.partials.content.artists')
@endsection

@push('scripts')
    <script src="{{ asset('js/site/artists.js') }}"></script>
@endpush
