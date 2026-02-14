<?php

namespace AgenticDebugger\Laravel\Transport;

interface TransportInterface
{
    public function send(array $payload): void;
}
