@props(['blogs'])

@php
    $fallbackImg = 'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=600&q=80';
@endphp

@foreach ($blogs as $blog)
  <a href="{{ route('blogs.show', $blog) }}" class="list-card" data-ssr-blog="{{ $blog->id }}">
    @if ($blog->image_url)
      <div class="list-img">
        <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" loading="lazy" decoding="async">
      </div>
    @endif
    <div class="list-body">
      <div class="list-kicker">مدونة</div>
      <h3 class="list-headline">{{ $blog->title }}</h3>
      @if ($blog->excerpt)
        <p class="list-excerpt">{{ $blog->excerpt }}</p>
      @endif
      <div class="list-meta">
        <span><b>{{ $blog->author ?: 'فريق التحرير' }}</b></span>
      </div>
    </div>
  </a>
@endforeach
