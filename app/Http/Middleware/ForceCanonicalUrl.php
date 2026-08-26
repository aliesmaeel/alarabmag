<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceCanonicalUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->environment('production') || ! $request->isMethodSafe()) {
            return $next($request);
        }

        // Controllers that already 301 to a canonical absolute URL: skip host/scheme
        // rewrite so http/www does not become a 2-hop chain.
        if ($this->defersCanonicalRedirect($request)) {
            return $next($request);
        }

        $canonicalRoot = rtrim((string) config('app.url'), '/');
        $canonicalHost = parse_url($canonicalRoot, PHP_URL_HOST);
        $canonicalScheme = parse_url($canonicalRoot, PHP_URL_SCHEME) ?: 'https';

        if (! $canonicalHost) {
            return $next($request);
        }

        $schemeMatches = strtolower($this->requestScheme($request)) === strtolower($canonicalScheme);
        $hostMatches = strcasecmp($request->getHost(), $canonicalHost) === 0;
        $path = $request->getPathInfo();
        $needsSlashTrim = $path !== '/' && str_ends_with($path, '/');

        if ($schemeMatches && $hostMatches && ! $needsSlashTrim) {
            return $next($request);
        }

        $canonicalPath = $needsSlashTrim ? rtrim($path, '/') : $path;
        $target = $canonicalRoot.$canonicalPath;
        if ($query = $request->getQueryString()) {
            $target .= '?'.$query;
        }

        return redirect()->to($target, 301);
    }

    protected function defersCanonicalRedirect(Request $request): bool
    {
        $path = $request->getPathInfo();

        return (bool) preg_match(
            '#^/(?:(?:blogs|news|fashion)/\d+|search/label/[^/]+|\d{4}/\d{2}/[^/]+\.html)$#',
            $path
        );
    }

    protected function requestScheme(Request $request): string
    {
        $forwardedProto = $request->headers->get('x-forwarded-proto');

        if (filled($forwardedProto)) {
            return trim(explode(',', $forwardedProto)[0]);
        }

        return $request->getScheme();
    }
}
