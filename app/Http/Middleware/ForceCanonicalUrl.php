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

        $canonicalRoot = rtrim((string) config('app.url'), '/');
        $canonicalHost = parse_url($canonicalRoot, PHP_URL_HOST);
        $canonicalScheme = parse_url($canonicalRoot, PHP_URL_SCHEME) ?: 'https';

        if (! $canonicalHost) {
            return $next($request);
        }

        $schemeMatches = strtolower($this->requestScheme($request)) === strtolower($canonicalScheme);
        $hostMatches = strcasecmp($request->getHost(), $canonicalHost) === 0;

        if ($schemeMatches && $hostMatches) {
            return $next($request);
        }

        return redirect()->to($canonicalRoot.$request->getRequestUri(), 301);
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
