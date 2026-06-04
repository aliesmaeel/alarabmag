@extends('layouts.site')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/interview-details.css') }}">
@endpush

@section('content')
    @include('site.partials.content.interview-details')
@endsection

@push('scripts')
    <script src="{{ asset('js/site/interview-details.js') }}" defer></script>
@endpush
