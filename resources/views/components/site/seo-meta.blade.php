@props(['seo', 'entity' => null])

@php
use App\Support\SiteBrand;

$pageUrl = $seo->canonical ?: url()->current();
@endphp

<title>{{ e($seo->title) }}</title>

@if($seo->description)
<meta name="description" content="{{ e($seo->description) }}">
@endif

@if($seo->keywords)
<meta name="keywords" content="{{ e($seo->keywords) }}">
@endif

<meta name="author" content="{{ e(SiteBrand::NAME_AR) }}">
<meta name="publisher" content="{{ e(SiteBrand::NAME_EN) }}">

@if($seo->robots)
<meta name="robots" content="{{ e($seo->robots) }}">
@else
<meta name="robots" content="index, follow, max-image-preview:large">
@endif

@if($seo->canonical)
<link rel="canonical" href="{{ e($seo->canonical) }}">
@endif

<link rel="alternate" hreflang="ar" href="{{ e($pageUrl) }}">
<link rel="alternate" hreflang="x-default" href="{{ e($pageUrl) }}">

<link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
<link rel="apple-touch-icon" href="{{ asset('logo.png') }}">

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

@foreach(app(\App\Services\SeoService::class)->jsonLd($seo, $entity) as $graph)
<script type="application/ld+json">
    {
        !!json_encode($graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) !!
    }
</script>
@endforeach