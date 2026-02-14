<?php

namespace FixStack\Laravel\Transport;

use FixStack\Laravel\Jobs\SendErrorJob;

class AsyncTransport implements TransportInterface
{
    public function send(array $payload): void
    {
        $connection = config('fixstack.queue_connection');

        SendErrorJob::dispatch($payload)->onConnection($connection);
    }
}
