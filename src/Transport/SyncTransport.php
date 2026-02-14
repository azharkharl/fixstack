<?php

namespace FixStack\Laravel\Transport;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncTransport implements TransportInterface
{
    public function send(array $payload): void
    {
        try {
            $endpoint = rtrim(config('fixstack.endpoint'), '/');

            Http::timeout(config('fixstack.timeout', 5))
                ->withHeaders([
                    'X-API-Key' => config('fixstack.api_key'),
                    'User-Agent' => 'FixStack-Laravel-SDK/1.0',
                ])
                ->post("{$endpoint}/api/v1/errors", $payload);
        } catch (\Throwable $e) {
            Log::channel('single')->debug('FixStack: failed to send error', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
