<?php

namespace FixStack\Laravel\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestCommand extends Command
{
    protected $signature = 'fixstack:test';

    protected $description = 'Test connection to the FixStack platform';

    public function handle(): int
    {
        $this->info('Testing FixStack connection...');
        $this->newLine();

        $apiKey = config('fixstack.api_key');

        if (!$apiKey) {
            $this->error('API key not configured. Set FIXSTACK_API_KEY in your .env file.');
            return 1;
        }

        $endpoint = rtrim(config('fixstack.endpoint'), '/');
        $this->line("  Endpoint: {$endpoint}");
        $this->line("  API Key:  {$this->maskKey($apiKey)}");
        $this->newLine();

        // Health check
        $this->line('1. Checking health endpoint...');
        try {
            $response = Http::timeout(5)->get("{$endpoint}/api/health");

            if ($response->successful()) {
                $this->info('   OK - Platform is reachable');
            } else {
                $this->error("   FAILED - Status: {$response->status()}");
                return 1;
            }
        } catch (\Throwable $e) {
            $this->error("   FAILED - {$e->getMessage()}");
            return 1;
        }

        // Send test error
        $this->line('2. Sending test error...');
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-API-Key' => $apiKey,
                    'User-Agent' => 'FixStack-Laravel-SDK/1.0',
                ])
                ->post("{$endpoint}/api/v1/errors", [
                    'class' => 'FixStack\\Test\\TestException',
                    'message' => 'This is a test error from the FixStack Laravel SDK.',
                    'level' => 'warning',
                    'stack_trace' => [
                        [
                            'file' => 'artisan',
                            'line' => 1,
                            'function' => 'handle',
                            'class' => self::class,
                        ],
                    ],
                    'app_context' => [
                        'laravel_version' => app()->version(),
                        'php_version' => PHP_VERSION,
                        'environment' => app()->environment(),
                    ],
                    'occurred_at' => now()->toIso8601String(),
                ]);

            if ($response->successful()) {
                $data = $response->json('data', []);
                $this->info('   OK - Test error sent successfully');
                $this->line("   Error ID:    " . ($data['error_id'] ?? 'N/A'));
                $this->line("   Fingerprint: " . ($data['fingerprint'] ?? 'N/A'));
            } else {
                $this->error("   FAILED - Status: {$response->status()}");
                $this->line("   Response: {$response->body()}");
                return 1;
            }
        } catch (\Throwable $e) {
            $this->error("   FAILED - {$e->getMessage()}");
            return 1;
        }

        $this->newLine();
        $this->info('All checks passed! FixStack is configured correctly.');

        return 0;
    }

    protected function maskKey(string $key): string
    {
        if (strlen($key) <= 8) {
            return str_repeat('*', strlen($key));
        }

        return substr($key, 0, 5) . str_repeat('*', strlen($key) - 8) . substr($key, -3);
    }
}
