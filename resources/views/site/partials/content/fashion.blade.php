
<section class="page-hero">
  <div class="page-hero-eyebrow">Fashion · موضة</div>
  <h1 class="page-hero-title">الموضة العربية <em>على المسرح العالمي</em></h1>
  <p class="page-hero-sub">من أسبوع باريس إلى منصات دبي — تقارير عن المصممين العرب والهوية والأناقة المعاصرة.</p>
  <div class="crumb"><a href="/">الرئيسية</a><span>›</span><span>الموضة العربية</span></div>
</section>

<section id="featured" class="feat-hero" style="display:none;border-bottom:1px solid var(--rule);">
  <div class="feat-hero-grid" id="featuredGrid"></div>
</section>

<div class="sh"><div class="sh-title">كل تقارير الموضة</div><div class="sh-rule"></div><span class="sh-more" id="resultsCount">—</span></div>
<section class="fashion-list-section">
  <div class="fashion-grid" id="fashionGrid">
    @if (($initialArticles ?? collect())->isNotEmpty())
      @foreach ($initialArticles as $article)
        <a href="{{ route('news.show', $article) }}" class="fash-card" data-ssr-article="{{ $article->id }}" style="text-decoration:none;color:inherit;">
          <div class="fash-img">
            <img src="{{ $article->image_url ?: 'https://images.unsplash.com/photo-1559181567-c3190ca9be46?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $article->title }}" loading="lazy" decoding="async">
          </div>
          <div class="fash-body">
            <div class="fash-kicker">{{ $article->category }}</div>
            <h3 class="fash-headline">{{ $article->title }}</h3>
            @if ($article->excerpt)<p class="fash-excerpt">{{ $article->excerpt }}</p>@endif
          </div>
        </a>
      @endforeach
    @else
      <div class="loading" style="grid-column:1/-1;padding:3rem;text-align:center;background:var(--cream);">جاري التحميل…</div>
    @endif
  </div>
</section>
