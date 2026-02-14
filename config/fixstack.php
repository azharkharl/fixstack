<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable Error Reporting
    |--------------------------------------------------------------------------
    */
    'enabled' => env('FIXSTACK_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | API Endpoint
    |--------------------------------------------------------------------------
    |
    | The URL of your FixStack platform instance.
    |
    */
    'endpoint' => env('FIXSTACK_ENDPOINT', 'https://app.fixstack.dev'),

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | Your project's API key (starts with pk_). Found in Project Settings.
    |
    */
    'api_key' => env('FIXSTACK_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Async Sending
    |--------------------------------------------------------------------------
    |
    | When true, errors are sent via a queued job for zero performance impact.
    | Set to false for synchronous sending (useful for testing).
    |
    */
    'async' => env('FIXSTACK_ASYNC', true),

    /*
    |--------------------------------------------------------------------------
    | Queue Connection
    |--------------------------------------------------------------------------
    |
    | The queue connection to use for async sending. Set to null to use
    | your application's default queue connection.
    |
    */
    'queue_connection' => env('FIXSTACK_QUEUE', null),

    /*
    |--------------------------------------------------------------------------
    | Environment Filtering
    |--------------------------------------------------------------------------
    |
    | Only report errors in these environments. Leave empty to report in all.
    |
    */
    'environments' => ['production', 'staging'],

    /*
    |--------------------------------------------------------------------------
    | Sampling Rate
    |--------------------------------------------------------------------------
    |
    | A float between 0.0 and 1.0 representing the percentage of errors to
    | report. 1.0 = report all errors, 0.5 = report 50%, 0.0 = report none.
    |
    */
    'sample_rate' => env('FIXSTACK_SAMPLE_RATE', 1.0),

    /*
    |--------------------------------------------------------------------------
    | Ignored Exceptions
    |--------------------------------------------------------------------------
    |
    | Exception classes that should never be reported.
    |
    */
    'ignored_exceptions' => [
        Illuminate\Auth\AuthenticationException::class,
        Illuminate\Validation\ValidationException::class,
        Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Timeout
    |--------------------------------------------------------------------------
    |
    | Timeout in seconds for API requests.
    |
    */
    'timeout' => 5,

    /*
    |--------------------------------------------------------------------------
    | Header Sanitization
    |--------------------------------------------------------------------------
    |
    | Header names containing these strings (case-insensitive) will have
    | their values replaced with [REDACTED] before sending.
    |
    */
    'sanitize_headers' => [
        'authorization',
        'cookie',
        'x-api-key',
        'x-auth-token',
        'x-csrf-token',
    ],

    /*
    |--------------------------------------------------------------------------
    | Body Sanitization
    |--------------------------------------------------------------------------
    |
    | Request body field names containing these strings (case-insensitive)
    | will have their values replaced with [REDACTED] before sending.
    |
    */
    'sanitize_body' => [
        'password',
        'password_confirmation',
        'token',
        'secret',
        'api_key',
        'credit_card',
        'card_number',
        'cvv',
        'ssn',
    ],

    /*
    |--------------------------------------------------------------------------
    | Breadcrumbs
    |--------------------------------------------------------------------------
    */
    'breadcrumbs' => [
        'enabled' => true,
        'max_items' => 50,
    ],

];
