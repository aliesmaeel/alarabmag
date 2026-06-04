@extends('layouts.site')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/interviews.css') }}">
@endpush

@section('content')
    @include('site.partials.content.interviews')
@endsection

@push('scripts')
    <script src="{{ asset('js/site/interviews.js') }}"></script>
@endpush
