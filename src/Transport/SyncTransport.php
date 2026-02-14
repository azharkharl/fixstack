<?php

namespace AgenticDebugger\Laravel\Transport;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncTransport implements TransportInterface
{
    public function send(array $payload): void
    {
        try {
            $endpoint = rtrim(config('agentic-debugger.endpoint'), '/');

            Http::timeout(config('agentic-debugger.timeout', 5))
                ->withHeaders([
                    'X-API-Key' => config('agentic-debugger.api_key'),
                    'User-Agent' => 'AgenticDebugger-Laravel-SDK/1.0',
                ])
                ->post("{$endpoint}/api/v1/errors", $payload);
        } catch (\Throwable $e) {
            Log::channel('single')->debug('Agentic Debugger: failed to send error', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
