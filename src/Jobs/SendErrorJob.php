<?php

namespace FixStack\Laravel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendErrorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;

    public int $timeout = 30;

    public array $backoff = [10, 30, 60];

    public function __construct(
        protected array $payload,
    ) {}

    public function handle(): void
    {
        $endpoint = rtrim(config('fixstack.endpoint'), '/');

        $response = Http::timeout(config('fixstack.timeout', 5))
            ->withHeaders([
                'X-API-Key' => config('fixstack.api_key'),
                'User-Agent' => 'FixStack-Laravel-SDK/1.0',
            ])
            ->post("{$endpoint}/api/v1/errors", $this->payload);

        if ($response->status() >= 500 || $response->status() === 429) {
            throw new \RuntimeException("FixStack API error: {$response->status()}");
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::channel('single')->warning('FixStack: failed to send error after retries', [
            'error' => $e->getMessage(),
        ]);
    }
}
