<?php

namespace AgenticDebugger\Laravel\Transport;

use AgenticDebugger\Laravel\Jobs\SendErrorJob;

class AsyncTransport implements TransportInterface
{
    public function send(array $payload): void
    {
        $connection = config('agentic-debugger.queue_connection');

        SendErrorJob::dispatch($payload)->onConnection($connection);
    }
}
