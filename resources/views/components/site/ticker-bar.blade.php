<div class="ticker-bar">
    <div class="ticker-label">{{ $label }}</div>
    <div class="ticker-wrap">
        <div class="ticker-track" id="tickerTrack" data-speed="{{ $speed }}">
            @php
                $copies = max(1, (int) ceil(4 / max(1, $texts->count())));
            @endphp
            @foreach ([false, true] as $duplicate)
                <div class="ticker-loop" @if($duplicate) aria-hidden="true" @endif>
                    @for ($copy = 0; $copy < $copies; $copy++)
                        @foreach ($texts as $text)
                            <span class="ticker-item">{{ $text }}</span>
                            <span class="ticker-sep">◆</span>
                        @endforeach
                    @endfor
                </div>
            @endforeach
        </div>
    </div>
</div>
