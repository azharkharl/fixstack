<?php

namespace AgenticDebugger\Laravel\Breadcrumbs;

class BreadcrumbRecorder
{
    protected array $breadcrumbs = [];

    public function record(string $message, string $category = 'default'): void
    {
        if (!config('agentic-debugger.breadcrumbs.enabled', true)) {
            return;
        }

        $this->breadcrumbs[] = new Breadcrumb(
            message: $message,
            category: $category,
            timestamp: now()->toIso8601String(),
        );

        $max = config('agentic-debugger.breadcrumbs.max_items', 50);

        if (count($this->breadcrumbs) > $max) {
            array_shift($this->breadcrumbs);
        }
    }

    public function get(): array
    {
        return array_map(fn (Breadcrumb $b) => $b->toArray(), $this->breadcrumbs);
    }

    public function clear(): void
    {
        $this->breadcrumbs = [];
    }
}
