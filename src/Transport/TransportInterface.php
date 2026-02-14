<?php

namespace FixStack\Laravel\Transport;

interface TransportInterface
{
    public function send(array $payload): void;
}
