@extends('layouts.site')

@php
    $fallbackCover = 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=900&q=80';
    $leader = $entries->first();
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/vote.css') }}">
@endpush

@section('content')
    <main id="main" class="vote-page" data-vote-root data-vote-url="{{ route('vote.store') }}"
        data-csrf="{{ csrf_token() }}" data-open="{{ $isOpen ? '1' : '0' }}" data-voted="{{ $votedEntryId ?: '' }}"
        data-flash="{{ session('vote_success') ?: session('vote_error') }}">

        <section class="page-hero vote-hero">
            <div class="page-hero-eyebrow">{{ $poll->eyebrow ?: 'Vote · أغنية العام' }}</div>
            <h1 class="page-hero-title">{{ $poll->title }}</h1>
            <p class="page-hero-sub">{{ $poll->subtitle }}</p>
            <div class="crumb">
                <a href="{{ url('/') }}">الرئيسية</a>
                <span>›</span>
                <span>أغنية العام</span>
            </div>
        </section>

        <div class="vote-stats" role="region" aria-label="إحصاءات التصويت">
            <div class="vote-stat">
                <span class="vote-stat-value" data-total-votes>{{ number_format($totalVotes) }}</span>
                <span class="vote-stat-label">صوت حتى الآن</span>
            </div>
            <div class="vote-stat-rule" aria-hidden="true"></div>
            <div class="vote-stat">
                <span class="vote-stat-value">{{ $entries->count() }}</span>
                <span class="vote-stat-label">أغانٍ مرشّحة</span>
            </div>
            <div class="vote-stat-rule" aria-hidden="true"></div>
            <div class="vote-stat">
                <span class="vote-stat-value">{{ $poll->year }}</span>
                <span class="vote-stat-label">إصدارات العام </span>
            </div>
            <div class="vote-stat-rule" aria-hidden="true"></div>
            <div class="vote-stat">
                <span class="vote-stat-value">1</span>
                <span class="vote-stat-label">صوت لكل قارئ</span>
            </div>
        </div>

        @if ($leader)
            <section class="vote-leader" aria-labelledby="vote-leader-title">
                <div class="vote-leader-media">
                    <img src="{{ $leader->image_url ?: $fallbackCover }}"
                        alt="{{ $leader->title }} — {{ $leader->artist }}" width="720" height="720">
                    <span class="vote-leader-rank" aria-hidden="true">01</span>
                </div>
                <div class="vote-leader-body">
                    <div class="vote-leader-kicker" id="vote-leader-title">المتصدر الآن</div>
                    <h2 class="vote-leader-title">{{ $leader->title }}</h2>
                    <p class="vote-leader-artist">{{ $leader->artist }}
                        @if ($leader->country)
                            <span>· {{ $leader->flag }} {{ $leader->country }}</span>
                        @endif
                    </p>
                    @if ($leader->excerpt)
                        <p class="vote-leader-excerpt">{{ $leader->excerpt }}</p>
                    @endif
                    <div class="vote-bar" role="img"
                        aria-label="نسبة الأصوات {{ $leader->shareOf($totalVotes) }} بالمئة">
                        <div class="vote-bar-fill" data-bar="{{ $leader->id }}"
                            style="width: {{ $leader->shareOf($totalVotes) }}%"></div>
                    </div>
                    <div class="vote-leader-meta">
                        <strong data-votes="{{ $leader->id }}">{{ number_format($leader->votes_count) }}</strong>
                        <span>صوت ·</span>
                        <span data-share="{{ $leader->id }}">{{ $leader->shareOf($totalVotes) }}%</span>
                    </div>
                    <div class="vote-leader-listen">
                        @include('site.partials.vote-listen', [
                            'entry' => $leader,
                            'cover' => $leader->image_url ?: $fallbackCover,
                        ])
                    </div>
                </div>
            </section>
        @endif

        <div class="sh dark-sh">
            <div class="sh-title">صنّف الأغاني المرشّحة</div>
            <div class="sh-rule"></div>
            <span class="sh-more" data-vote-status>
                @if ($votedEntryId)
                    تم تسجيل صوتك
                @elseif ($isOpen)
                    اختر أغنية واحدة
                @else
                    التصويت مغلق
                @endif
            </span>
        </div>

        <ol class="vote-list">
            @foreach ($entries as $index => $entry)
                @php
                    $rank = $index + 1;
                    $share = $entry->shareOf($totalVotes);
                    $isVoted = $votedEntryId === $entry->id;
                    $cover = $entry->image_url ?: $fallbackCover;
                @endphp
                <li class="vote-card @if ($isVoted) is-voted @endif @if ($votedEntryId && !$isVoted) is-locked @endif"
                    data-entry="{{ $entry->id }}" data-title="{{ $entry->title }}" data-artist="{{ $entry->artist }}"
                    style="--i: {{ $index }}">
                    <span class="vote-card-num" data-rank="{{ $entry->id }}"
                        aria-hidden="true">{{ str_pad((string) $rank, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="vote-card-cover">
                        <img src="{{ $cover }}" alt="" width="160" height="160"
                            loading="{{ $index < 2 ? 'eager' : 'lazy' }}">
                    </div>
                    <div class="vote-card-body">
                        <h2 class="vote-card-title">{{ $entry->title }}</h2>
                        <p class="vote-card-artist">
                            {{ $entry->artist }}
                            @if ($entry->country)
                                <span class="vote-card-country">{{ $entry->flag }} {{ $entry->country }}</span>
                            @endif
                        </p>
                        @if ($entry->excerpt)
                            <p class="vote-card-excerpt">{{ $entry->excerpt }}</p>
                        @endif
                        <div class="vote-bar" role="img" aria-label="نسبة الأصوات {{ $share }} بالمئة">
                            <div class="vote-bar-fill" data-bar="{{ $entry->id }}" style="width: {{ $share }}%">
                            </div>
                        </div>
                        <div class="vote-card-meta">
                            <span><strong
                                    data-votes="{{ $entry->id }}">{{ number_format($entry->votes_count) }}</strong>
                                صوت</span>
                            <span data-share="{{ $entry->id }}">{{ $share }}%</span>
                        </div>
                    </div>
                    <div class="vote-card-action">
                        <form method="POST" action="{{ route('vote.store') }}" data-vote-form>
                            @csrf
                            <input type="hidden" name="entry_id" value="{{ $entry->id }}">
                            <button type="submit" class="vote-btn" data-vote-btn="{{ $entry->id }}"
                                @disabled(!$isOpen || $votedEntryId) aria-pressed="{{ $isVoted ? 'true' : 'false' }}"
                                aria-label="صوّت لـ {{ $entry->title }} — {{ $entry->artist }}">
                                <svg class="vote-btn-icon" viewBox="0 0 24 24" width="18" height="18"
                                    aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    @if ($isVoted)
                                        <path d="M5 12l5 5L20 7" />
                                    @else
                                        <path d="M12 5v14M5 12h14" />
                                    @endif
                                </svg>
                                <span>{{ $isVoted ? 'صوتك' : 'صوّت' }}</span>
                            </button>
                        </form>
                        @include('site.partials.vote-listen', ['entry' => $entry, 'cover' => $cover])
                    </div>
                </li>
            @endforeach
        </ol>

        <aside class="vote-rules" aria-labelledby="vote-rules-title">
            <h2 id="vote-rules-title">قواعد التصويت</h2>
            <ul>
                <li>صوت واحد لكل بريد إلكتروني خلال فترة الاستفتاء.</li>
                <li>القائمة من أبرز أغاني 2026 حسب منصات البث والنقد الموسيقي.</li>
                <li>النتيجة تُحدَّث مباشرة بعد كل صوت.</li>
            </ul>
        </aside>

        <audio data-vote-audio preload="auto"></audio>

        @include('site.partials.vote-player')

        <div class="vote-toast" data-vote-toast role="status" aria-live="polite" hidden></div>

        @include('site.partials.vote-dialog')
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('js/site/vote.js') }}" defer></script>
@endpush
