@props(['seo'])

@php
    /** @var \App\Support\SeoMeta $seo */
@endphp

<title>{{ e($seo->title) }}</title>

@if($seo->description)
    <meta name="description" content="{{ e($seo->description) }}">
@endif

@if($seo->keywords)
    <meta name="keywords" content="{{ e($seo->keywords) }}">
@endif

@if($seo->robots)
    <meta name="robots" content="{{ e($seo->robots) }}">
@endif

@if($seo->canonical)
    <link rel="canonical" href="{{ e($seo->canonical) }}">
@endif

<meta property="og:locale" content="{{ e($seo->ogLocale) }}">
<meta property="og:type" content="{{ e($seo->ogType) }}">
<meta property="og:title" content="{{ e($seo->ogTitleResolved()) }}">

@if($seo->ogDescriptionResolved())
    <meta property="og:description" content="{{ e($seo->ogDescriptionResolved()) }}">
@endif

@if($seo->ogImage)
    <meta property="og:image" content="{{ e($seo->ogImage) }}">
@endif

@if($seo->ogUrl)
    <meta property="og:url" content="{{ e($seo->ogUrl) }}">
@endif

@if($seo->ogSiteName)
    <meta property="og:site_name" content="{{ e($seo->ogSiteName) }}">
@endif

<meta name="twitter:card" content="{{ e($seo->twitterCard) }}">
<meta name="twitter:title" content="{{ e($seo->ogTitleResolved()) }}">

@if($seo->ogDescriptionResolved())
    <meta name="twitter:description" content="{{ e($seo->ogDescriptionResolved()) }}">
@endif

@if($seo->ogImage)
    <meta name="twitter:image" content="{{ e($seo->ogImage) }}">
@endif
