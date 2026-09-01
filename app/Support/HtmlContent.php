<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMXPath;

class HtmlContent
{
    public static function isHtml(?string $value): bool
    {
        return filled($value) && (bool) preg_match(
            '/<\/?(p|h\d|ul|ol|li|blockquote|img|a|strong|em|br|figure|div)\b/i',
            $value
        );
    }

    public static function render(?string $value): string
    {
        if (! filled($value)) {
            return '';
        }

        if (! self::isHtml($value)) {
            return nl2br(e($value), false);
        }

        return self::embedInlineImages($value);
    }

    public static function embedInlineImages(string $html): string
    {
        if (! preg_match('/<(a|figure)\b/i', $html)) {
            return $html;
        }

        $previous = libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML(
            '<?xml encoding="UTF-8"><div id="html-content-root">'.$html.'</div>',
            \LIBXML_NOERROR | \LIBXML_NOWARNING
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($dom);
        $anchors = [];

        foreach ($xpath->query('//a[@href]') as $anchor) {
            if ($anchor instanceof DOMElement) {
                $anchors[] = $anchor;
            }
        }

        foreach ($anchors as $anchor) {
            if ($anchor->getElementsByTagName('img')->length > 0) {
                continue;
            }

            $href = html_entity_decode($anchor->getAttribute('href'), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if ($href === '' || $href === '#') {
                $href = self::imageUrlFromTrixFigure($anchor->parentNode instanceof DOMElement ? $anchor->parentNode : null) ?? '';
            }

            if (! self::isImageHref($href)) {
                continue;
            }

            $img = $dom->createElement('img');
            $img->setAttribute('src', $href);
            $img->setAttribute('alt', self::altFromAttachmentLabel($anchor->textContent));
            $img->setAttribute('loading', 'lazy');
            $anchor->parentNode?->replaceChild($img, $anchor);
        }

        $captions = [];

        foreach ($xpath->query('//figcaption') as $caption) {
            if ($caption instanceof DOMElement) {
                $captions[] = $caption;
            }
        }

        foreach ($captions as $caption) {
            $caption->parentNode?->removeChild($caption);
        }

        $root = $dom->getElementById('html-content-root');

        if (! $root) {
            return $html;
        }

        $out = '';

        foreach ($root->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }

        return $out;
    }

    private static function isImageHref(string $href): bool
    {
        $path = parse_url($href, PHP_URL_PATH);

        if (! is_string($path) || $path === '') {
            $path = $href;
        }

        return (bool) preg_match('/\.(jpe?g|png|gif|webp|avif|bmp|svg)$/i', $path);
    }

    private static function altFromAttachmentLabel(string $text): string
    {
        $text = trim(preg_replace('/\s+[\d.,]+\s*K?B\s*$/i', '', $text) ?? '');

        return $text;
    }

    private static function imageUrlFromTrixFigure(?DOMElement $node): ?string
    {
        if (! $node || strtolower($node->tagName) !== 'figure') {
            return null;
        }

        $raw = html_entity_decode($node->getAttribute('data-trix-attachment'), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if ($raw === '') {
            return null;
        }

        $data = json_decode($raw, true);

        if (! is_array($data)) {
            return null;
        }

        foreach (['url', 'href'] as $key) {
            $value = trim((string) ($data[$key] ?? ''));

            if ($value !== '' && self::isImageHref($value)) {
                return $value;
            }
        }

        return null;
    }
}
