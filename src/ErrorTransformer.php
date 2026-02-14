<?php

namespace FixStack\Laravel;

use FixStack\Laravel\Context\ContextCollector;

class ErrorTransformer
{
    public function __construct(
        protected ContextCollector $contextCollector,
        protected Sanitizer $sanitizer,
    ) {}

    public function transform(\Throwable $e, array $breadcrumbs): array
    {
        return [
            'class' => substr(get_class($e), 0, 500),
            'message' => substr($e->getMessage(), 0, 2000),
            'level' => $this->determineLevel($e),
            'stack_trace' => $this->transformStackTrace($e->getTrace()),
            'request_context' => $this->collectRequestContext(),
            'user_context' => $this->contextCollector->collectUser(),
            'app_context' => $this->contextCollector->collectApp(),
            'breadcrumbs' => $breadcrumbs,
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    protected function transformStackTrace(array $trace): array
    {
        return array_map(fn (array $frame) => [
            'file' => $frame['file'] ?? '[internal]',
            'line' => $frame['line'] ?? 0,
            'function' => $frame['function'] ?? null,
            'class' => $frame['class'] ?? null,
        ], $trace);
    }

    protected function collectRequestContext(): ?array
    {
        try {
            $request = request();

            if (!$request || app()->runningInConsole()) {
                return null;
            }

            return [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'headers' => $this->sanitizer->sanitizeHeaders($request->headers->all()),
                'body' => $this->sanitizer->sanitizeBody($request->all()),
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    protected function determineLevel(\Throwable $e): string
    {
        $class = get_class($e);

        if (stripos($class, 'Fatal') !== false || stripos($class, 'Critical') !== false) {
            return 'critical';
        }

        if (stripos($class, 'Warning') !== false) {
            return 'warning';
        }

        return 'error';
    }
}
