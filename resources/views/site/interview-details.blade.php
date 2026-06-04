@extends('layouts.site')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/interview-details.css') }}">
@endpush

@section('content')
    @include('site.partials.content.interview-details')
@endsection

@push('scripts')
    <script>window.SITE_INTERVIEW_SLUG = @json($interviewSlug);</script>
    <script src="{{ asset('js/site/interview-details.js') }}"></script>
@endpush
