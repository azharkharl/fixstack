<?php

namespace AgenticDebugger\Laravel;

use AgenticDebugger\Laravel\Breadcrumbs\BreadcrumbRecorder;
use AgenticDebugger\Laravel\Transport\TransportInterface;
use Illuminate\Support\Facades\Log;

class ErrorReporter
{
    public function __construct(
        protected ErrorTransformer $transformer,
        protected TransportInterface $transport,
        protected BreadcrumbRecorder $breadcrumbs,
    ) {}

    public function report(\Throwable $exception): void
    {
        try {
            if (!config('agentic-debugger.enabled', true)) {
                return;
            }

            if (!$this->shouldReportEnvironment()) {
                return;
            }

            if ($this->shouldIgnore($exception)) {
                return;
            }

            if (!$this->shouldSample()) {
                return;
            }

            $payload = $this->transformer->transform($exception, $this->breadcrumbs->get());

            $this->transport->send($payload);
        } catch (\Throwable $e) {
            Log::channel('single')->debug('Agentic Debugger: failed to report error', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function shouldReportEnvironment(): bool
    {
        $environments = config('agentic-debugger.environments', []);

        if (empty($environments)) {
            return true;
        }

        return in_array(app()->environment(), $environments);
    }

    protected function shouldIgnore(\Throwable $exception): bool
    {
        $ignored = config('agentic-debugger.ignored_exceptions', []);

        foreach ($ignored as $ignoredClass) {
            if ($exception instanceof $ignoredClass) {
                return true;
            }
        }

        return false;
    }

    protected function shouldSample(): bool
    {
        $rate = (float) config('agentic-debugger.sample_rate', 1.0);

        if ($rate >= 1.0) {
            return true;
        }

        if ($rate <= 0.0) {
            return false;
        }

        return mt_rand() / mt_getrandmax() <= $rate;
    }
}
