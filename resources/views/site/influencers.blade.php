@extends('layouts.site')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/influencers.css') }}">
@endpush

@section('content')
    @include('site.partials.content.influencers')
@endsection

@push('scripts')
    <script src="{{ asset('js/site/influencers.js') }}"></script>
@endpush
