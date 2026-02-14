<?php

namespace FixStack\Laravel\Context;

class ContextCollector
{
    public function __construct(
        protected UserContextCollector $user,
        protected AppContextCollector $app,
    ) {}

    public function collectUser(): ?array
    {
        return $this->user->collect();
    }

    public function collectApp(): array
    {
        return $this->app->collect();
    }
}
