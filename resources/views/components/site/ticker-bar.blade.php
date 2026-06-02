<div class="ticker-bar">
    <div class="ticker-label">{{ $label }}</div>
    <div class="ticker-wrap">
        <div class="ticker-track" id="tickerTrack">
            @foreach ([false, true] as $duplicate)
                <div style="display:flex;gap:4rem;flex-shrink:0" @if($duplicate) aria-hidden="true" @endif>
                    @forelse ($entries as $entry)
                        <a href="{{ $entry['url'] }}" class="ticker-item">
                            <b>{{ $entry['kicker'] }}:</b> {{ $entry['title'] }}
                        </a>
                        <span class="ticker-sep">◆</span>
                    @empty
                        <span class="ticker-item"><b>مجلة العرب:</b> تابع آخر الأخبار من فريق التحرير</span>
                    @endforelse
                </div>
            @endforeach
        </div>
    </div>
</div>
