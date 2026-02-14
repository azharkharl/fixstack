<?php

namespace AgenticDebugger\Laravel;

use AgenticDebugger\Laravel\Breadcrumbs\BreadcrumbRecorder;
use AgenticDebugger\Laravel\Console\TestCommand;
use AgenticDebugger\Laravel\Transport\AsyncTransport;
use AgenticDebugger\Laravel\Transport\SyncTransport;
use AgenticDebugger\Laravel\Transport\TransportInterface;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AgenticDebuggerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/agentic-debugger.php',
            'agentic-debugger',
        );

        $this->app->singleton(BreadcrumbRecorder::class);

        $this->app->bind(TransportInterface::class, function () {
            return config('agentic-debugger.async', true)
                ? new AsyncTransport()
                : new SyncTransport();
        });

        $this->app->singleton(ErrorReporter::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/agentic-debugger.php' => config_path('agentic-debugger.php'),
        ], 'agentic-debugger-config');

        if ($this->app->runningInConsole()) {
            $this->commands([TestCommand::class]);
        }

        $this->registerExceptionHandler();
    }

    protected function registerExceptionHandler(): void
    {
        if (!config('agentic-debugger.enabled', true)) {
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
            Log::channel('single')->debug('Agentic Debugger: failed to register exception handler', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
