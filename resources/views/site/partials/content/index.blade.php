@php
    $fallbackImg = 'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=1400&q=85';
    $hero = $home['hero'] ?? null;
    $counts = $home['counts'] ?? [];
@endphp

<div id="top"></div>

<!-- HERO -->
<div class="hero-section">
  <div class="hero-grid">
    <div class="hero-sidebar">
      @foreach ($home['sidebarArticles'] ?? [] as $story)
        <a href="{{ route('news.show', $story) }}" class="sidebar-story" style="display:block;text-decoration:none;color:inherit;">
          <div class="sidebar-img">
            <img src="{{ $story->image_url ?: $fallbackImg }}" alt="{{ $story->title }}" loading="lazy" decoding="async">
          </div>
          <div class="story-kicker">{{ $story->category }}</div>
          <h3 class="story-headline">{{ $story->title }}</h3>
          <div class="story-meta">{{ $story->read_time ?: '5 دقائق' }} · {{ $story->category }}</div>
        </a>
      @endforeach
    </div>
    @if ($hero)
      <a href="{{ route('news.show', $hero) }}" class="hero-main" style="display:block;text-decoration:none;color:inherit;">
        <div class="hero-img-wrap">
          <img id="heroImg" src="{{ $hero->image_url ?: $fallbackImg }}" alt="{{ $hero->title }}" loading="eager" decoding="async">
          <div class="hero-overlay"></div>
          <div class="hero-content">
            <div class="hero-kicker">✦ {{ $hero->subtitle ?: $hero->category }} · {{ $hero->region ?: 'عربي' }}</div>
            <h2 class="hero-headline">{{ $hero->title }}</h2>
            @if ($hero->excerpt)
              <p class="hero-deck">{{ $hero->excerpt }}</p>
            @endif
            <div class="hero-byline">بقلم <b>{{ $hero->author ?: 'فريق التحرير' }}</b> · {{ $hero->read_time ?: '5 دقائق' }}</div>
          </div>
        </div>
      </a>
    @endif
  </div>
</div>

<!-- CATEGORIES -->
<div class="sh">
  <div class="sh-title">أقسام المجلة</div>
  <div class="sh-rule"></div>
</div>
<div class="cats-section">
  <h2 class="cats-title">اكتشف عالم <em>الإنسان العربي</em></h2>
  <p class="cats-sub">من المؤثرين إلى الأطباء، من الفنانين إلى رجال الأعمال — قصص تُلهم</p>
  <div class="cats-grid">
    <a href="#influencers" class="cat-card">
      <span class="cat-icon">📱</span>
      <div class="cat-name">المؤثرون العرب</div>
      <div class="cat-name-en">Arab Influencers</div>
      <div class="cat-count">{{ $counts['influencers'] ?? 0 }} ملف</div>
    </a>
    <a href="#artists" class="cat-card">
      <span class="cat-icon">🎭</span>
      <div class="cat-name">الفنانون العرب</div>
      <div class="cat-name-en">Arab Artists</div>
      <div class="cat-count">{{ $counts['artists'] ?? 0 }} ملف</div>
    </a>
    <a href="{{ route('business.index') }}" class="cat-card">
      <span class="cat-icon">💼</span>
      <div class="cat-name">الأعمال العربية</div>
      <div class="cat-name-en">Arab Business</div>
      <div class="cat-count">{{ $counts['business'] ?? 0 }} قصة</div>
    </a>
    <a href="#doctors" class="cat-card">
      <span class="cat-icon">🩺</span>
      <div class="cat-name">أطباء عرب</div>
      <div class="cat-name-en">Arab Doctors</div>
      <div class="cat-count">{{ $counts['doctors'] ?? 0 }} ملف</div>
    </a>
    <a href="#news" class="cat-card">
      <span class="cat-icon">📰</span>
      <div class="cat-name">الأخبار</div>
      <div class="cat-name-en">News</div>
      <div class="cat-count">{{ $counts['articles'] ?? 0 }} مقال</div>
    </a>
    <a href="#blogs" class="cat-card">
      <span class="cat-icon">✍️</span>
      <div class="cat-name">المدونات</div>
      <div class="cat-name-en">Blogs</div>
      <div class="cat-count">{{ $counts['blogs'] ?? 0 }} مقال</div>
    </a>
    <a href="{{ route('fashion.index') }}" class="cat-card">
      <span class="cat-icon">👗</span>
      <div class="cat-name">الموضة العربية</div>
      <div class="cat-name-en">Arab Fashion</div>
      <div class="cat-count">{{ $counts['fashion'] ?? 0 }} تقرير</div>
    </a>
  </div>
</div>

<!-- INFLUENCERS -->
<div id="influencers">
  <div class="sh">
    <div class="sh-title">المؤثرون العرب</div>
    <div class="sh-rule"></div><a href="/influencers" class="sh-more">عرض الكل →</a>
  </div>
  <div class="influencers-grid">
    @foreach ($home['influencers'] ?? [] as $person)
      <a href="{{ route('influencers.show', $person) }}" class="inf-card" style="text-decoration:none;color:inherit;">
        <div class="inf-img">
          <img src="{{ $person->image_url ?: $fallbackImg }}" alt="{{ $person->name }}" loading="lazy" decoding="async">
          <div class="inf-img-ov"></div>
        </div>
        <div class="inf-body">
          <div class="inf-cat">{{ $person->role }} · {{ $person->country }}</div>
          <div class="inf-name">{{ $person->name }}</div>
          @if ($person->handle)
            <div class="inf-handle">{{ $person->handle }}</div>
          @endif
          @if ($person->followers)
            <div class="inf-followers">{{ $person->followers }}</div>
            <div class="inf-followers-label">متابع على {{ $person->platform ?: 'المنصات' }}</div>
          @endif
        </div>
      </a>
    @endforeach
  </div>
</div>

<!-- BUSINESS -->
<div id="business">
  <div class="sh">
    <div class="sh-title">الأعمال العربية</div>
    <div class="sh-rule"></div><a href="{{ route('business.index') }}" class="sh-more">جميع القصص →</a>
  </div>
  <div class="featured-section">
    <div class="featured-grid">
      @foreach ($home['businessArticles'] ?? [] as $i => $article)
        <a href="{{ route('news.show', $article) }}" class="feat-card {{ $i === 0 ? 'large' : '' }}" style="text-decoration:none;color:inherit;">
          <div class="feat-img {{ $i === 0 ? 'h400' : 'h280' }}">
            <img src="{{ $article->image_url ?: $fallbackImg }}" alt="{{ $article->title }}" loading="lazy" decoding="async">
            <div class="feat-img-overlay"></div>
            <div class="feat-img-content">
              <div class="feat-kicker">{{ $article->subtitle ?: $article->category }}</div>
              @if ($i === 0)
                <h2 class="feat-headline feat-headline-lg">{{ $article->title }}</h2>
              @else
                <h3 class="feat-headline">{{ $article->title }}</h3>
              @endif
              <div class="feat-meta">بقلم {{ $article->author }} · {{ $article->read_time ?: '5 دقائق' }}</div>
            </div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</div>

<!-- ARTISTS -->
<div id="artists" class="artists-band">
  <div class="artists-text">
    <div class="artists-label">الفنانون العرب</div>
    <h2 class="artists-headline">أصوات تُعبّر عن<br><em>روح الأمة</em></h2>
    <p class="artists-body">من مسارح بيروت إلى صالات العرض في باريس، الفنان العربي يفرض حضوره على الخريطة العالمية بشروطه الخاصة.</p>
    <a href="/artists" class="btn-gold">استكشف الفنانين العرب →</a>
  </div>
  <div class="artists-profiles">
    @foreach ($home['artists'] ?? [] as $i => $person)
      <a href="{{ route('artists.show', $person) }}" class="ap" style="text-decoration:none;color:inherit;">
        <div class="ap-img"><img src="{{ $person->image_url ?: $fallbackImg }}" alt="{{ $person->name }}" loading="lazy" decoding="async"></div>
        <div class="ap-body">
          <div class="ap-num">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</div>
          <div class="ap-role">{{ $person->role }}</div>
          <div class="ap-name">{{ $person->name }}</div>
          <div class="ap-country">{{ $person->flag }} {{ $person->country }}</div>
          @if ($person->excerpt)
            <p class="ap-excerpt">{{ $person->excerpt }}</p>
          @endif
        </div>
      </a>
    @endforeach
  </div>
</div>

<!-- DOCTORS -->
<div id="doctors">
  <div class="sh">
    <div class="sh-title">أطباء عرب</div>
    <div class="sh-rule"></div><a href="/doctors" class="sh-more">عرض الكل →</a>
  </div>
  <div class="doctors-grid">
    @foreach ($home['doctors'] ?? [] as $person)
      <a href="{{ route('doctors.show', $person) }}" class="doc-card" style="text-decoration:none;color:inherit;">
        <div class="doc-img">
          <img src="{{ $person->image_url ?: $fallbackImg }}" alt="{{ $person->name }}" loading="lazy" decoding="async">
          <div class="doc-img-ov"></div>
        </div>
        <div class="doc-body">
          <div class="doc-spec">{{ $person->specialty ?: $person->role }} · {{ $person->hospital }}</div>
          <div class="doc-name">{{ $person->name }}</div>
          <div class="doc-hospital">{{ $person->flag }} {{ $person->country }}</div>
          @if ($person->badge)
            <div class="doc-badge">{{ $person->badge }}</div>
          @endif
        </div>
      </a>
    @endforeach
  </div>
</div>

<!-- FASHION -->
<div id="fashion">
  <div class="sh">
    <div class="sh-title">الموضة العربية</div>
    <div class="sh-rule"></div><a href="{{ route('fashion.index') }}" class="sh-more">كل الموضة →</a>
  </div>
  <div class="fashion-grid">
    @foreach ($home['fashionArticles'] ?? [] as $article)
      <a href="{{ route('news.show', $article) }}" class="fash" style="text-decoration:none;color:inherit;">
        <img src="{{ $article->image_url ?: $fallbackImg }}" alt="{{ $article->title }}" loading="lazy" decoding="async">
        <div class="fash-overlay"></div>
        <div class="fash-body">
          <div class="fash-kicker">{{ $article->subtitle ?: $article->category }}</div>
          <h3 class="fash-headline">{{ $article->title }}</h3>
        </div>
      </a>
    @endforeach
  </div>
</div>

<!-- NEWS & BLOGS -->
<div id="news">
  <div class="sh">
    <div class="sh-title">الأخبار</div>
    <div class="sh-rule"></div><a href="/news" class="sh-more">كل الأخبار →</a>
  </div>
  <div class="news-grid" id="newsGrid">
    @foreach ($home['newsArticles'] ?? [] as $article)
      <a href="{{ route('news.show', $article) }}" class="list-card">
        @if ($article->image_url)
          <div class="list-img"><img src="{{ $article->image_url }}" alt="{{ $article->title }}" loading="lazy" decoding="async"></div>
        @endif
        <div class="list-body">
          <div class="list-kicker">{{ $article->category }}</div>
          <h3 class="list-headline">{{ $article->title }}</h3>
          @if ($article->excerpt)<p class="list-excerpt">{{ $article->excerpt }}</p>@endif
          <div class="list-meta"><span>{{ $article->read_time ?: '5 دقائق' }}</span><span><b>{{ $article->author }}</b></span></div>
        </div>
      </a>
    @endforeach
  </div>
</div>

<div id="blogs">
  <div class="sh" style="margin-top:0">
    <div class="sh-title">من المدونات</div>
    <div class="sh-rule"></div><a href="/blogs" class="sh-more">كل المدونات →</a>
  </div>
  <div class="news-grid" id="blogsGridHome">
    @foreach ($home['blogs'] ?? [] as $blog)
      <a href="{{ route('blogs.show', $blog) }}" class="list-card">
        @if ($blog->image_url)
          <div class="list-img"><img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" loading="lazy" decoding="async"></div>
        @endif
        <div class="list-body">
          <div class="list-kicker">مدونة</div>
          <h3 class="list-headline">{{ $blog->title }}</h3>
          @if ($blog->excerpt)<p class="list-excerpt">{{ $blog->excerpt }}</p>@endif
          <div class="list-meta"><span><b>{{ $blog->author }}</b></span></div>
        </div>
      </a>
    @endforeach
  </div>
</div>
