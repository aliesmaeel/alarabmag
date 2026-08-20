@php
    $audioUrl = $entry->audioUrl();
    $listenUrl = $entry->listen_url;
    $cover = $cover ?? ($entry->image_url ?: 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=200&q=80');
@endphp
@if ($audioUrl)
    <button type="button"
        class="vote-listen"
        data-play-src="{{ $audioUrl }}"
        data-play-id="{{ $entry->id }}"
        data-play-title="{{ $entry->title }}"
        data-play-artist="{{ $entry->artist }}"
        data-play-cover="{{ $cover }}"
        aria-pressed="false"
        aria-label="استمع إلى {{ $entry->title }} — {{ $entry->artist }}">
        <svg class="vote-listen-icon" data-play-icon viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" fill="currentColor">
            <path d="M8 5.14v14l11-7-11-7z"/>
        </svg>
        <span data-play-label>اسمع</span>
    </button>
@elseif ($listenUrl)
    <a class="vote-listen" href="{{ $listenUrl }}" target="_blank" rel="noopener noreferrer">
        اسمع
        <svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 18V5l12-2v13"/>
            <circle cx="6" cy="18" r="3"/>
            <circle cx="18" cy="16" r="3"/>
        </svg>
    </a>
@endif
