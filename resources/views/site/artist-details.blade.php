@extends('layouts.site')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/artist-details.css') }}">
@endpush

@section('content')
    @include('site.partials.content.person-details')
@endsection
