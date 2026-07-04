<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @isset($seo)
        <x-site.seo-meta :seo="$seo" />
    @else
        <title>{{ $issue->name ?? 'المجلة' }} — مجلة العرب</title>
    @endisset
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/site/magazine.css') }}">
</head>

<body class="magazine-reader-body">
    <header class="magazine-reader-bar">
        <a href="{{ route('magazine.index') }}" class="magazine-reader-back">← العودة إلى الأعداد</a>
        <h1 class="magazine-reader-title">{{ $issue->name }}</h1>
    </header>

    <iframe
        src="{{ $htmlUrl }}"
        title="{{ $issue->name }}"
        class="magazine-frame"
        loading="lazy"
    ></iframe>
</body>

</html>
