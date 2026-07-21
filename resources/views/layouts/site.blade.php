<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-19YYY1LW0J"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-19YYY1LW0J');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @isset($seo)
    <x-site.seo-meta :seo="$seo" :entity="$jsonLdEntity ?? null" />
    @else
    <title>@yield('title', 'مجلة العرب | Al Arab Magazine — المجلة العربية الأولى')</title>
    <meta name="description" content="مجلة العرب (Al Arab Magazine) — المجلة العربية الأولى للإنسان العربي المتميّز.">
    @endisset
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Cairo:wght@300;400;500;600;700;900&family=Amiri:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/site/sidebar.css') }}">
    @unless(request()->routeIs('home'))
    <link rel="stylesheet" href="{{ asset('css/site/chrome.css') }}">
    @endunless
    @stack('styles')
</head>

<body>
    @if($showPreloader ?? false)
    <x-site.preloader />
    @endif

    <x-site.cursor />

    @if($showTicker ?? false)
    <x-site.ticker :label="$tickerLabel ?? null" />
    @endif

    <x-site.header :active="$activeNav ?? null" />

    <x-site.adsense-leaderboard />

    <x-site.page-sidebar :active-nav="$activeNav ?? null" />

    @yield('content')

    <x-site.newsletter
        :eyebrow="$newsletterEyebrow ?? 'اشترك في النشرة'"
        :headline="$newsletterHeadline ?? null"
        :sub="$newsletterSub ?? null" />

    <x-site.footer :variant="$footerVariant ?? 'full'" />

    @unless($skipGsap ?? false)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    @endunless
    <script src="{{ asset('js/site/chrome.js') }}" defer></script>
    <script src="{{ asset('js/site/sidebar.js') }}" defer></script>
    @stack('scripts')
</body>

</html>