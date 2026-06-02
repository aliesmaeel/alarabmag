@extends('layouts.site')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/doctors.css') }}">
@endpush

@section('content')
    @include('site.partials.content.doctors')
@endsection

@push('scripts')
    <script src="{{ asset('js/site/doctors.js') }}"></script>
@endpush
