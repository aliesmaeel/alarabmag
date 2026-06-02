@extends('layouts.site')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/artist-details.css') }}">
@endpush

@section('content')
    @include('site.partials.content.artist-details')
@endsection

@push('scripts')
    <script>window.SITE_PERSON_ID = {{ $personId }};</script>
    <script src="{{ asset('js/site/artist-details.js') }}"></script>
@endpush
