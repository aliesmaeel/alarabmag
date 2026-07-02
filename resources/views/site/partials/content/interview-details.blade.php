@php
    $isYouTube = ($videoSource ?? 's3') === 'youtube' && filled($youtubeEmbedUrl ?? null);
    $hasS3Video = ! $isYouTube && filled($videoUrl);
    $videoType = $hasS3Video && preg_match('/\.webm(\?|$)/i', $videoUrl) ? 'video/webm' : ($hasS3Video && preg_match('/\.ogg(\?|$)/i', $videoUrl) ? 'video/ogg' : 'video/mp4');
    $poster = $thumbnailUrl ?: null;
@endphp

<main id="main">
  <section class="interview-layout">
    <div class="interview-layout__main">
      <header class="interview-head">
        <div class="interview-head__eyebrow">مقابلة · {{ $interview->category ?: 'عام' }}</div>
        <h1 class="interview-head__title">{{ $interview->title }}</h1>
        <div class="interview-head__meta">
          {{ $interview->created_at?->locale('ar')->translatedFormat('j F Y') }}
          · {{ number_format($interview->views ?? 0) }} مشاهدة
        </div>
      </header>

      @if ($isYouTube)
        <div class="video-player video-player--youtube">
          <div class="video-player__youtube">
            <iframe
              src="{{ $youtubeEmbedUrl }}"
              title="{{ $interview->title }}"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              allowfullscreen
              referrerpolicy="strict-origin-when-cross-origin"
            ></iframe>
          </div>
          @if (filled($youtubeWatchUrl ?? null))
            <p class="video-player__youtube-fallback">
              <a href="{{ $youtubeWatchUrl }}" target="_blank" rel="noopener noreferrer">شاهد على يوتيوب ↗</a>
            </p>
          @endif
        </div>
      @elseif ($hasS3Video)
        <div class="video-player" id="videoPlayer" data-share-title="{{ $interview->title }}">
          <div class="video-player__media">
          <video id="interviewVideo" playsinline preload="none" @if($poster) poster="{{ $poster }}" @endif>
            <source src="{{ $videoUrl }}" type="{{ $videoType }}">
          </video>
          <div class="video-player__overlay" id="videoOverlay">
            <button type="button" class="video-player__big-play" id="bigPlayBtn" aria-label="تشغيل">▶</button>
          </div>
          </div>
          <div class="video-player__bar">
            <button type="button" class="vp-btn" id="playBtn" title="تشغيل / إيقاف" aria-label="تشغيل">▶</button>
            <div class="vp-progress">
              <input type="range" class="vp-progress__range" id="progressRange" min="0" max="100" value="0" step="0.1" aria-label="التقدم">
              <div class="vp-progress__track"><div class="vp-progress__fill" id="progressFill"></div></div>
            </div>
            <span class="vp-time" id="timeDisplay">0:00 / 0:00</span>
            <button type="button" class="vp-btn" id="muteBtn" title="كتم الصوت" aria-label="كتم">🔊</button>
            <label class="vp-speed">
              <span class="vp-speed__label">السرعة</span>
              <select id="speedSelect" aria-label="سرعة التشغيل">
                <option value="0.5">0.5×</option>
                <option value="0.75">0.75×</option>
                <option value="1" selected>1×</option>
                <option value="1.25">1.25×</option>
                <option value="1.5">1.5×</option>
                <option value="2">2×</option>
              </select>
            </label>
            <button type="button" class="vp-btn" id="fullscreenBtn" title="ملء الشاشة" aria-label="ملء الشاشة">⛶</button>
            <div class="vp-share-wrap">
              <button type="button" class="vp-btn" id="shareBtn" title="مشاركة" aria-label="مشاركة">⎘</button>
              <div class="vp-share-menu" id="shareMenu" hidden>
                <a href="#" id="shareTwitter" class="vp-share-item" target="_blank" rel="noopener">
                  <span class="vp-share-item__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                  </span>
                  <span class="vp-share-item__label">تويتر</span>
                </a>
                <a href="#" id="shareFacebook" class="vp-share-item" target="_blank" rel="noopener">
                  <span class="vp-share-item__icon vp-share-item__icon--fb" aria-hidden="true">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                  </span>
                  <span class="vp-share-item__label">فيسبوك</span>
                </a>
                <a href="#" id="shareWhatsapp" class="vp-share-item" target="_blank" rel="noopener">
                  <span class="vp-share-item__icon vp-share-item__icon--wa" aria-hidden="true">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                  </span>
                  <span class="vp-share-item__label">واتساب</span>
                </a>
                <button type="button" id="shareCopy" class="vp-share-item">
                  <span class="vp-share-item__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>
                  </span>
                  <span class="vp-share-item__label">نسخ الرابط</span>
                </button>
              </div>
            </div>
          </div>
          <p class="video-error" id="videoError" hidden>تعذّر تحميل الفيديو.</p>
        </div>
      @else
        <div class="video-player video-player--fallback">
          <p>لا يوجد فيديو لهذه المقابلة.</p>
        </div>
      @endif

      @if (filled($interview->description))
        <div class="interview-about">
          <h2 class="interview-about__title">عن المقابلة</h2>
          <div class="interview-about__body">{!! nl2br(e($interview->description)) !!}</div>
        </div>
      @endif
    </div>

    <aside class="interview-layout__side">
      <div class="news-side">
        <div class="news-side__head">
          <h2 class="news-side__title">آخر <em>الأخبار</em></h2>
          <a href="/news" class="news-side__more">الكل ←</a>
        </div>
        <div class="news-side__list">
          @forelse ($latestArticles as $article)
            <a href="{{ route('news.show', $article) }}" class="news-side-item">
              <div class="news-side-item__img">
                @if ($article->image_url)
                  <img src="{{ $article->image_url }}" alt="" loading="lazy" decoding="async" width="80" height="60">
                @else
                  <span class="news-side-item__img-placeholder" aria-hidden="true"></span>
                @endif
              </div>
              <div class="news-side-item__body">
                <div class="news-side-item__kicker">{{ $article->category ?: 'عام' }}</div>
                <h3 class="news-side-item__title">{{ $article->title }}</h3>
                <span class="news-side-item__meta">{{ $article->created_at?->locale('ar')->diffForHumans() }}</span>
              </div>
            </a>
          @empty
            <p class="news-side-empty">لا توجد أخبار حالياً.</p>
          @endforelse
        </div>
      </div>
    </aside>
  </section>
</main>
