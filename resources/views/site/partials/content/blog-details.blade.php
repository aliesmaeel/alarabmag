@php
    $tags = $blog->tags ? array_filter(array_map('trim', explode(',', $blog->tags))) : [];
    $bodyIsHtml = filled($blog->body) && preg_match('/<\/?(p|h\d|ul|ol|li|blockquote|img|a|strong|em|br)\b/i', $blog->body);
    $shareUrl = route('blogs.show', $blog);
@endphp

<main id="main">
  <section class="blog-hero">
    <div class="blog-hero-inner">
      <div class="blog-hero-eyebrow">مدونة العرب</div>
      <h1 class="blog-hero-title">{{ $blog->title }}</h1>
      @if (filled($blog->excerpt))
        <p class="blog-hero-excerpt">{{ $blog->excerpt }}</p>
      @endif
      <div class="blog-hero-meta">
        @if ($blog->author_img)
          <div class="blog-hero-avatar">
            <img src="{{ $blog->author_img }}" alt="{{ $blog->author }}" loading="lazy" decoding="async">
          </div>
        @endif
        <div class="blog-hero-meta-text">
          بقلم <b>{{ $blog->author ?: 'فريق التحرير' }}</b><br>
          {{ $blog->created_at?->locale('ar')->translatedFormat('j F Y') }}
          · {{ number_format($blog->views ?? 0) }} مشاهدة
        </div>
      </div>
    </div>
  </section>

  @if ($blog->image_url)
    <div class="blog-cover">
      <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" loading="lazy" decoding="async">
    </div>
  @endif

  <section class="blog-section">
    <article class="blog-wrap">
      <div class="blog-body">
        @if (filled($blog->body))
          @if ($bodyIsHtml)
            {!! $blog->body !!}
          @else
            {!! nl2br(e($blog->body)) !!}
          @endif
        @else
          <p>لا يوجد محتوى لعرضه.</p>
        @endif
      </div>

      @if (count($tags))
        <div class="blog-tags">
          @foreach ($tags as $tag)
            <a class="blog-tag" href="/blogs?tag={{ urlencode($tag) }}">#{{ $tag }}</a>
          @endforeach
        </div>
      @endif

      <div class="blog-share">
        <div class="share-label">شارك المقال</div>
        <div class="share-list">
          <a class="share-btn" target="_blank" rel="noopener"
             href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($blog->title) }}">𝕏</a>
          <a class="share-btn" target="_blank" rel="noopener"
             href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}">f</a>
          <a class="share-btn" target="_blank" rel="noopener"
             href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}">in</a>
          <a class="share-btn" target="_blank" rel="noopener"
             href="https://wa.me/?text={{ urlencode($blog->title . ' — ' . $shareUrl) }}">w</a>
          <button type="button" class="share-btn" id="copyBtn" title="نسخ الرابط">⎘</button>
        </div>
      </div>

      @if (filled($blog->author_bio) || filled($blog->author_img))
        <div class="author-card">
          @if ($blog->author_img)
            <div class="author-card-img">
              <img src="{{ $blog->author_img }}" alt="{{ $blog->author }}" loading="lazy" decoding="async">
            </div>
          @endif
          <div class="author-card-info">
            <div class="author-card-eyebrow">عن الكاتب</div>
            <div class="author-card-name">{{ $blog->author ?: 'فريق التحرير' }}</div>
            <p class="author-card-bio">{{ $blog->author_bio ?: 'كاتب في مجلة العرب.' }}</p>
          </div>
        </div>
      @endif
    </article>
  </section>

  @if ($relatedBlogs->isNotEmpty())
    <section class="related-section">
      <h2 class="related-title">مقالات <em>قد تعجبك</em></h2>
      <div class="related-grid">
        @foreach ($relatedBlogs as $related)
          <a href="{{ route('blogs.show', $related) }}" class="list-card">
            @if ($related->image_url)
              <div class="list-img">
                <img src="{{ $related->image_url }}" alt="{{ $related->title }}" loading="lazy" decoding="async">
              </div>
            @endif
            <div class="list-body">
              <div class="list-kicker">مدونة · {{ $related->author ?: 'فريق التحرير' }}</div>
              <h3 class="list-headline">{{ $related->title }}</h3>
              @if ($related->excerpt)
                <p class="list-excerpt">{{ $related->excerpt }}</p>
              @endif
              <div class="list-meta">
                <span>{{ number_format($related->views ?? 0) }} مشاهدة</span>
                <span><b>{{ $related->author ?: 'فريق التحرير' }}</b></span>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    </section>
  @endif
</main>
