<?php

namespace AgenticDebugger\Laravel\Context;

use Illuminate\Support\Facades\Auth;

class UserContextCollector
{
    public function collect(): ?array
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        return [
            'id' => $user->getAuthIdentifier(),
            'email' => $user->email ?? null,
        ];
    }
}
