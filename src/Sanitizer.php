<?php

namespace AgenticDebugger\Laravel;

class Sanitizer
{
    public function sanitizeHeaders(array $headers): array
    {
        $patterns = config('agentic-debugger.sanitize_headers', []);

        foreach ($headers as $key => $value) {
            foreach ($patterns as $pattern) {
                if (stripos($key, $pattern) !== false) {
                    $headers[$key] = ['[REDACTED]'];
                    break;
                }
            }
        }

        return $headers;
    }

    public function sanitizeBody(array $data): array
    {
        $patterns = config('agentic-debugger.sanitize_body', []);

        return $this->redactRecursive($data, $patterns);
    }

    protected function redactRecursive(array $data, array $patterns): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->redactRecursive($value, $patterns);
                continue;
            }

            foreach ($patterns as $pattern) {
                if (stripos($key, $pattern) !== false) {
                    $data[$key] = '[REDACTED]';
                    break;
                }
            }
        }

        return $data;
    }
}
