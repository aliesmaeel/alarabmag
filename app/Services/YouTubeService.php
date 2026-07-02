<?php

namespace App\Services;

class YouTubeService
{
    public function isYouTubeUrl(?string $value): bool
    {
        return filled($value) && $this->extractVideoId($value) !== null;
    }

    public function extractVideoId(?string $url): ?string
    {
        if (! filled($url)) {
            return null;
        }

        $url = trim($url);

        if (preg_match('#(?:youtube\.com/(?:watch\?.*v=|embed/|shorts/)|youtu\.be/)([a-zA-Z0-9_-]{11})#', $url, $matches)) {
            return $matches[1];
        }

        if (preg_match('#^([a-zA-Z0-9_-]{11})$#', $url)) {
            return $url;
        }

        return null;
    }

    public function isShortUrl(?string $url): bool
    {
        return filled($url) && preg_match('#youtube\.com/shorts/#i', trim($url));
    }

    public function canonicalUrl(?string $url): ?string
    {
        $id = $this->extractVideoId($url);

        if (! $id) {
            return null;
        }

        return $this->isShortUrl($url)
            ? 'https://www.youtube.com/shorts/'.$id
            : 'https://www.youtube.com/watch?v='.$id;
    }

    public function embedUrl(?string $url): ?string
    {
        $id = $this->extractVideoId($url);

        if (! $id) {
            return null;
        }

        $params = http_build_query([
            'rel' => '0',
            'modestbranding' => '1',
            'playsinline' => '1',
        ]);

        return 'https://www.youtube.com/embed/'.$id.'?'.$params;
    }

    public function thumbnailUrl(?string $url): ?string
    {
        $id = $this->extractVideoId($url);

        if (! $id) {
            return null;
        }

        // hqdefault is available for regular uploads and Shorts; maxresdefault often 404s.
        return 'https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg';
    }

    public function videoSource(?string $videoUrl): string
    {
        return $this->isYouTubeUrl($videoUrl) ? 'youtube' : 's3';
    }
}
