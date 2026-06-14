@php
    $fallbackImg = 'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=600&q=80';
    $bodyIsHtml = filled($person->bio) && preg_match('/<\/?(p|h\d|ul|ol|li|blockquote|img|a|strong|em|br)\b/i', $person->bio);
    $categoryLabels = [
        'doctor' => ['Doctor Profile · ملف طبيب', 'doctors.index', 'أطباء'],
        'influencer' => ['Influencer Profile · ملف مؤثر', 'influencers.index', 'المؤثرون'],
        'artist' => ['Artist Profile · ملف فنان', 'artists.index', 'الفنانون'],
        'business' => ['Business Profile · ملف أعمال', 'business.index', 'الأعمال'],
    ];
    [$eyebrow, $indexRoute, $relatedLabel] = $categoryLabels[$person->category] ?? ['Profile', 'home', ''];
    $heroImg = $person->image_url ?: $fallbackImg;
@endphp

<main id="main">
  <section class="profile-hero">
    <div class="profile-hero-bg" style="background-image:url('{{ $heroImg }}')"></div>
    <div class="profile-hero-grid">
      <div class="profile-portrait">
        <img src="{{ $heroImg }}" alt="{{ $person->name }}" loading="eager" decoding="async">
        @if ($person->flag)
          <span class="profile-portrait-flag">{{ $person->flag }}</span>
        @endif
      </div>
      <div class="profile-info">
        <div class="profile-eyebrow">{{ $eyebrow }}</div>
        @if ($person->category === 'doctor')
          <div class="profile-spec">{{ $person->specialty ?: $person->role ?: 'طب عام' }}</div>
        @elseif ($person->category === 'influencer')
          <div class="profile-spec">{{ $person->role ?: 'مؤثر رقمي' }}</div>
        @else
          <div class="profile-spec">{{ $person->role ?: '' }}</div>
        @endif
        <h1 class="profile-name">{{ $person->name }}</h1>
        @if ($person->name_en)
          <div class="profile-name-en">{{ $person->name_en }}</div>
        @endif
        @if ($person->excerpt)
          <p class="profile-tagline">{{ $person->excerpt }}</p>
        @endif
        <div class="profile-meta">
          @if ($person->category === 'doctor' && $person->hospital)
            <span>🏥 <b>{{ $person->hospital }}</b></span>
          @endif
          @if ($person->country)
            <span>📍 <b>{{ $person->country }}</b></span>
          @endif
          @if ($person->badge)
            <span>🏅 <b>{{ $person->badge }}</b></span>
          @endif
          @if ($person->category === 'influencer' && $person->handle)
            <span>{{ $person->handle }} · {{ $person->platform }}</span>
          @endif
          @if ($person->category === 'business' && $person->company)
            <span>💼 <b>{{ $person->company }}</b></span>
          @endif
        </div>
      </div>
    </div>
  </section>

  @if ($person->stat || $person->specialty || $person->hospital || $person->badge || $person->followers)
    <section class="profile-stats">
      <div class="profile-stats-inner">
        @if ($person->stat)
          <div class="pstat"><div class="pstat-num">{{ $person->stat }}</div><div class="pstat-lbl">{{ $person->stat_label ?: 'إنجاز' }}</div></div>
        @endif
        @if ($person->specialty)
          <div class="pstat"><div class="pstat-num small">{{ $person->specialty }}</div><div class="pstat-lbl">التخصص</div></div>
        @endif
        @if ($person->hospital)
          <div class="pstat"><div class="pstat-num small">{{ $person->hospital }}</div><div class="pstat-lbl">المستشفى</div></div>
        @endif
        @if ($person->badge)
          <div class="pstat"><div class="pstat-num small">{{ $person->badge }}</div><div class="pstat-lbl">التكريم</div></div>
        @endif
        @if ($person->followers)
          <div class="pstat"><div class="pstat-num">{{ $person->followers }}</div><div class="pstat-lbl">متابع</div></div>
        @endif
      </div>
    </section>
  @endif

  <section class="profile-body-section">
    <article class="profile-body-wrap">
      <div class="profile-body">
        @if (filled($person->bio))
          @if ($bodyIsHtml)
            {!! $person->bio !!}
          @else
            {!! nl2br(e($person->bio)) !!}
          @endif
        @else
          <p>لم تتم إضافة سيرة بعد.</p>
        @endif
      </div>
    </article>
  </section>

  @if ($relatedPeople->isNotEmpty())
    <section class="related-section">
      <h2 class="related-title">{{ $relatedLabel }} <em>آخرون</em></h2>
      <div class="related-grid">
        @foreach ($relatedPeople as $related)
          @php
            $relatedRoute = match ($related->category) {
              'doctor' => 'doctors.show',
              'influencer' => 'influencers.show',
              'artist' => 'artists.show',
              'business' => 'business.show',
              default => null,
            };
          @endphp
          @if ($relatedRoute)
          <a href="{{ route($relatedRoute, $related) }}" class="doc-list-card">
            <div class="doc-list-img">
              <img src="{{ $related->image_url ?: $fallbackImg }}" alt="{{ $related->name }}" loading="lazy" decoding="async">
              @if ($related->flag)
                <span class="doc-list-img-flag">{{ $related->flag }}</span>
              @endif
              <div class="doc-list-img-ov"></div>
            </div>
            <div class="doc-list-body">
              <div class="doc-list-spec">{{ $related->specialty ?: $related->role ?: '' }}</div>
              <h3 class="doc-list-name">{{ $related->name }}</h3>
              <div class="doc-list-hospital">{{ $related->hospital ?: $related->country ?: '' }}</div>
              @if ($related->badge)
                <span class="doc-list-badge">{{ $related->badge }}</span>
              @endif
            </div>
          </a>
          @endif
        @endforeach
      </div>
    </section>
  @endif
</main>
