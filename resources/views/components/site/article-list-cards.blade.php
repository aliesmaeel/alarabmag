@props(['articles'])

@php
    $fallbackImg = 'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=600&q=80';
@endphp

@foreach ($articles as $article)
  <a href="{{ route('news.show', $article) }}" class="list-card" data-ssr-article="{{ $article->id }}">
    @if ($article->image_url)
      <div class="list-img">
        <img src="{{ $article->image_url }}" alt="{{ $article->title }}" loading="lazy" decoding="async">
      </div>
    @endif
    <div class="list-body">
      <div class="list-kicker">{{ $article->category ?: 'عام' }}</div>
      <h3 class="list-headline">{{ $article->title }}</h3>
      @if ($article->excerpt)
        <p class="list-excerpt">{{ $article->excerpt }}</p>
      @endif
      <div class="list-meta">
        <span>{{ $article->read_time ?: '5 دقائق' }}</span>
        <span><b>{{ $article->author ?: 'فريق التحرير' }}</b></span>
      </div>
    </div>
  </a>
@endforeach
