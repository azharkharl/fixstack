<?php

namespace FixStack\Laravel\Context;

class AppContextCollector
{
    public function collect(): array
    {
        return [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment(),
        ];
    }
}
