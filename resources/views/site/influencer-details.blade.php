@extends('layouts.site')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/influencer-details.css') }}">
@endpush

@section('content')
    @include('site.partials.content.influencer-details')
@endsection

@push('scripts')
    <script>window.SITE_PERSON_ID = {{ $personId }};</script>
    <script src="{{ asset('js/site/influencer-details.js') }}"></script>
@endpush
