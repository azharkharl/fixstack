<?php

namespace AgenticDebugger\Laravel;

use AgenticDebugger\Laravel\Breadcrumbs\BreadcrumbRecorder;

class AgenticDebugger
{
    public static function breadcrumb(string $message, string $category = 'default'): void
    {
        app(BreadcrumbRecorder::class)->record($message, $category);
    }

    public static function clearBreadcrumbs(): void
    {
        app(BreadcrumbRecorder::class)->clear();
    }
}
