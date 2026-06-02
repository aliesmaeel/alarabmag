@extends('layouts.site')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/index.css') }}">
@endpush

@section('content')
    @include('site.partials.content.index')
@endsection

@push('scripts')
    <script src="{{ asset('js/site/index.js') }}"></script>
@endpush
