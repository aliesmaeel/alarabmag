<?php

namespace App\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Outbound HTTP with IPv4 preference (fixes cURL error 6 on some Linux/PHP setups).
 */
class OutboundHttp
{
    public static function client(int $timeout = 30): PendingRequest
    {
        return Http::timeout($timeout)->withOptions([
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
        ]);
    }
}
