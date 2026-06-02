@extends('layouts.site')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/doctor-details.css') }}">
@endpush

@section('content')
    @include('site.partials.content.doctor-details')
@endsection

@push('scripts')
    <script>window.SITE_PERSON_ID = {{ $personId }};</script>
    <script src="{{ asset('js/site/business-details.js') }}"></script>
@endpush
