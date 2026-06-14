@props(['people', 'category'])

@php
    $fallbackImg = match ($category) {
        'doctor' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=600&q=80',
        default => 'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=600&q=80',
    };
    $routeName = match ($category) {
        'doctor' => 'doctors.show',
        'influencer' => 'influencers.show',
        'artist' => 'artists.show',
        'business' => 'business.show',
        default => 'home',
    };
@endphp

@foreach ($people as $person)
  <a href="{{ route($routeName, $person) }}" class="doc-list-card" data-ssr-person="{{ $person->id }}">
    <div class="doc-list-img">
      <img src="{{ $person->image_url ?: $fallbackImg }}" alt="{{ $person->name }}" loading="lazy" decoding="async">
      @if ($person->flag)
        <span class="doc-list-img-flag">{{ $person->flag }}</span>
      @endif
      <div class="doc-list-img-ov"></div>
    </div>
    <div class="doc-list-body">
      <div class="doc-list-spec">{{ $person->specialty ?: $person->role ?: '' }}</div>
      <h3 class="doc-list-name">{{ $person->name }}</h3>
      <div class="doc-list-hospital">{{ $person->hospital ?: $person->country ?: $person->company ?: '' }}</div>
      @if ($person->badge)
        <span class="doc-list-badge">{{ $person->badge }}</span>
      @endif
    </div>
  </a>
@endforeach
