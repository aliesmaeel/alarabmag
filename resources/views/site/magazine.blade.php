@extends('layouts.site')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/magazine.css') }}">
@endpush

@section('content')
    @include('site.partials.content.magazine')
@endsection
