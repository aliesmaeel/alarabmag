<?php

namespace App\Support;

class SeoMeta
{
    public function __construct(
        public string $title,
        public ?string $description = null,
        public ?string $keywords = null,
        public ?string $canonical = null,
        public ?string $ogTitle = null,
        public ?string $ogDescription = null,
        public ?string $ogImage = null,
        public ?string $ogUrl = null,
        public string $ogType = 'website',
        public ?string $ogSiteName = null,
        public string $ogLocale = 'ar_AE',
        public string $twitterCard = 'summary_large_image',
        public ?string $robots = null,
    ) {}

    public function ogTitleResolved(): string
    {
        return $this->ogTitle ?: $this->title;
    }

    public function ogDescriptionResolved(): ?string
    {
        return $this->ogDescription ?: $this->description;
    }
}
