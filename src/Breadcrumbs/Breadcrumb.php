<?php

namespace FixStack\Laravel\Breadcrumbs;

class Breadcrumb
{
    public function __construct(
        public readonly string $message,
        public readonly string $category,
        public readonly string $timestamp,
    ) {}

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'category' => $this->category,
            'timestamp' => $this->timestamp,
        ];
    }
}
