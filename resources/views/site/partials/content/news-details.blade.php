@php
    $bodyIsHtml = filled($article->body) && preg_match('/<\/?(p|h\d|ul|ol|li|blockquote|img|a|strong|em|br)\b/i', $article->body);
    $shareUrl = route('news.show', $article);
    $heroImg = $article->image_url ?: 'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=1400&q=85';
@endphp

<main id="main">
  <section class="article-hero">
    <img src="{{ $heroImg }}" alt="{{ $article->title }}" loading="eager" decoding="async">
    <div class="article-hero-overlay"></div>
    <div class="article-hero-inner">
      <div class="article-kicker">✦ {{ $article->category ?: 'عام' }}@if($article->region) · {{ $article->region }}@endif</div>
      <h1 class="article-title">{{ $article->title }}</h1>
      @if (filled($article->subtitle))
        <p class="article-subtitle">{{ $article->subtitle }}</p>
      @endif
      <div class="article-byline">
        <span>بقلم <b>{{ $article->author ?: 'فريق التحرير' }}</b></span>
        <span class="byline-sep">·</span>
        <span>{{ $article->created_at?->locale('ar')->translatedFormat('j F Y') }}</span>
        <span class="byline-sep">·</span>
        <span>{{ $article->read_time ?: '5 دقائق' }} للقراءة</span>
        <span class="byline-sep">·</span>
        <span>{{ number_format($article->views ?? 0) }} مشاهدة</span>
      </div>
    </div>
  </section>

  <section class="article-section">
    <article class="article-wrap">
      @if (filled($article->excerpt))
        <p class="article-lede">{{ $article->excerpt }}</p>
      @endif

      <div class="article-body">
        @if (filled($article->body))
          @if ($bodyIsHtml)
            {!! $article->body !!}
          @else
            {!! nl2br(e($article->body)) !!}
          @endif
        @else
          <p>لا يوجد محتوى لعرضه.</p>
        @endif
      </div>

      <div class="article-share">
        <div class="share-label">شارك المقال</div>
        <div class="share-list">
          <a class="share-btn" target="_blank" rel="noopener"
             href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($article->title) }}">𝕏</a>
          <a class="share-btn" target="_blank" rel="noopener"
             href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}">f</a>
          <a class="share-btn" target="_blank" rel="noopener"
             href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}">in</a>
          <a class="share-btn" target="_blank" rel="noopener"
             href="https://wa.me/?text={{ urlencode($article->title . ' — ' . $shareUrl) }}">w</a>
          <button type="button" class="share-btn" id="copyBtn" title="نسخ الرابط">⎘</button>
        </div>
      </div>
    </article>
  </section>

  @if ($relatedArticles->isNotEmpty())
    <section class="related-section">
      <h2 class="related-title">قصص <em>ذات صلة</em></h2>
      <div class="related-grid">
        @foreach ($relatedArticles as $related)
          <a href="{{ route('news.show', $related) }}" class="list-card">
            @if ($related->image_url)
              <div class="list-img">
                <img src="{{ $related->image_url }}" alt="{{ $related->title }}" loading="lazy" decoding="async">
              </div>
            @endif
            <div class="list-body">
              <div class="list-kicker">{{ $related->category ?: 'عام' }}</div>
              <h3 class="list-headline">{{ $related->title }}</h3>
              @if ($related->excerpt)
                <p class="list-excerpt">{{ $related->excerpt }}</p>
              @endif
              <div class="list-meta">
                <span>{{ $related->read_time ?: '5 دقائق' }}</span>
                <span><b>{{ $related->author ?: 'فريق التحرير' }}</b></span>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    </section>
  @endif
</main>
