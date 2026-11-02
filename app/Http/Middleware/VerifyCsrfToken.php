<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Square Webhook は Square サーバーからの POST なので CSRF トークンなし
        "payment/webhook/square",
        // Mailgun Webhook
        "webhooks/mailgun",
    ];
}
