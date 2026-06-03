@props(['active' => null])

@php
    use App\Support\HomeSections;

    $onHome = request()->routeIs('home');
    $items = HomeSections::items();
@endphp

<header class="masthead">
    <div class="masthead-top">

        <div class="logo-wrap">
            <a href="{{ $onHome ? '#top' : url('/') }}" class="logo-link" aria-label="مجلة العرب — Al Arab Magazine">
                <img src="{{ asset('logo.png') }}" alt="مجلة العرب — Al Arab Magazine" class="site-logo">
            </a>
        </div>
        <div style="display:flex;align-items:center;gap:1.5rem;">

            <a href="{{ $onHome ? '#newsletter' : url('/#newsletter') }}" class="btn-subscribe">اشترك الآن</a>
        </div>
    </div>
    <nav class="nav-bar" aria-label="التنقل الرئيسي">
        @foreach ($items as $item)
            <a href="{{ HomeSections::href($item, $onHome) }}" @class(['active' => $active === $item['key']])
                data-section="{{ $item['id'] }}">{{ $item['label'] }}</a>
            @if (!$loop->last)
                <div class="nav-divider"></div>
            @endif
        @endforeach
    </nav>
</header>
