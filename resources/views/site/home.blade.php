@extends('layouts.site')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/index.css') }}">
@endpush

@section('content')
    <h1 class="sr-only">مجلة العرب — Al Arab Magazine</h1>
    @include('site.partials.content.index')
@endsection

@push('scripts')
    <script src="{{ asset('js/site/index.js') }}"></script>
@endpush
