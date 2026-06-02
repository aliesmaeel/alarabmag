@extends('layouts.site')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/blogs.css') }}">
@endpush

@section('content')
    @include('site.partials.content.blogs')
@endsection

@push('scripts')
    <script src="{{ asset('js/site/blogs.js') }}"></script>
@endpush
