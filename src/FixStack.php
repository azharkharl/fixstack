<?php

namespace FixStack\Laravel;

use FixStack\Laravel\Breadcrumbs\BreadcrumbRecorder;

class FixStack
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
