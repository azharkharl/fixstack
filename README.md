# Agentic Debugger Laravel SDK

AI-powered error monitoring for Laravel applications. Captures exceptions and sends them to the [Agentic Debugger](https://agentic-debugger.com) platform for automated root cause analysis.

## Requirements

- PHP 8.1+
- Laravel 10, 11, or 12

## Installation

```bash
composer require agentic-debugger/laravel
```

The service provider is auto-discovered — no manual registration needed.

## Configuration

1. Add your credentials to `.env`:

```env
AGENTIC_DEBUGGER_API_KEY=pk_your_api_key_here
AGENTIC_DEBUGGER_ENDPOINT=https://app.agentic-debugger.com
```

2. (Optional) Publish the config file:

```bash
php artisan vendor:publish --tag=agentic-debugger-config
```

3. Test the connection:

```bash
php artisan agentic-debugger:test
```

## Usage

The SDK automatically captures all unhandled exceptions. No code changes required.

### Breadcrumbs

Track user actions leading up to an error:

```php
use AgenticDebugger\Laravel\AgenticDebugger;

AgenticDebugger::breadcrumb('User clicked checkout', 'user-action');
AgenticDebugger::breadcrumb('Payment processing started', 'payment');
AgenticDebugger::breadcrumb('Stripe API called', 'http');
```

Breadcrumbs are attached to the next error that occurs and provide context for AI analysis.

### What Gets Captured

Each error report includes:

- **Exception**: class name, message, full stack trace
- **Request**: URL, HTTP method, headers, body (sensitive fields redacted)
- **User**: authenticated user ID and email
- **App**: Laravel version, PHP version, environment
- **Breadcrumbs**: recent actions leading up to the error

## Configuration Options

| Option | Env Variable | Default | Description |
|---|---|---|---|
| `enabled` | `AGENTIC_DEBUGGER_ENABLED` | `true` | Enable/disable error reporting |
| `endpoint` | `AGENTIC_DEBUGGER_ENDPOINT` | `https://app.agentic-debugger.com` | Platform API URL |
| `api_key` | `AGENTIC_DEBUGGER_API_KEY` | — | Project API key (from Settings) |
| `async` | `AGENTIC_DEBUGGER_ASYNC` | `true` | Send errors via queue (recommended) |
| `queue_connection` | `AGENTIC_DEBUGGER_QUEUE` | `null` | Queue connection (null = default) |
| `sample_rate` | `AGENTIC_DEBUGGER_SAMPLE_RATE` | `1.0` | 0.0–1.0, percentage of errors to report |
| `environments` | — | `['production', 'staging']` | Only report in these environments |
| `timeout` | — | `5` | HTTP timeout in seconds |

### Ignored Exceptions

By default, the following exceptions are not reported:

- `Illuminate\Auth\AuthenticationException`
- `Illuminate\Validation\ValidationException`
- `Symfony\Component\HttpKernel\Exception\NotFoundHttpException`

Customize in `config/agentic-debugger.php`:

```php
'ignored_exceptions' => [
    \Illuminate\Auth\AuthenticationException::class,
    \Illuminate\Validation\ValidationException::class,
    \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
    \App\Exceptions\IgnoredException::class, // your own
],
```

### Data Sanitization

Sensitive data is automatically redacted before sending. Configure patterns in `config/agentic-debugger.php`:

```php
// Headers containing these strings are redacted
'sanitize_headers' => ['authorization', 'cookie', 'x-api-key'],

// Body fields containing these strings are redacted
'sanitize_body' => ['password', 'token', 'secret', 'credit_card', 'ssn'],
```

## Async vs Sync

**Async (default):** Errors are sent via a queued job. Zero performance impact on your application. Requires a queue worker running (`php artisan queue:work`).

**Sync:** Errors are sent immediately during the request. Useful for testing or when no queue is available. Set `AGENTIC_DEBUGGER_ASYNC=false`.

## License

MIT
