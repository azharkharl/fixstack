<?php

namespace FixStack\Laravel;

use FixStack\Laravel\Breadcrumbs\BreadcrumbRecorder;
use FixStack\Laravel\Console\TestCommand;
use FixStack\Laravel\Transport\AsyncTransport;
use FixStack\Laravel\Transport\SyncTransport;
use FixStack\Laravel\Transport\TransportInterface;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class FixStackServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/fixstack.php',
            'fixstack',
        );

        $this->app->singleton(BreadcrumbRecorder::class);

        $this->app->bind(TransportInterface::class, function () {
            return config('fixstack.async', true)
                ? new AsyncTransport()
                : new SyncTransport();
        });

        $this->app->singleton(ErrorReporter::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/fixstack.php' => config_path('fixstack.php'),
        ], 'fixstack-config');

        if ($this->app->runningInConsole()) {
            $this->commands([TestCommand::class]);
        }

        $this->registerExceptionHandler();
    }

    protected function registerExceptionHandler(): void
    {
        if (!config('fixstack.enabled', true)) {
            return;
        }

        try {
            $this->app->make(ExceptionHandler::class)
                ->reportable(function (\Throwable $e) {
                    try {
                        app(ErrorReporter::class)->report($e);
                    } catch (\Throwable) {
                        // Never throw during error reporting
                    }

                    return false; // Allow other reporters to continue
                });
        } catch (\Throwable $e) {
            Log::channel('single')->debug('FixStack: failed to register exception handler', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
