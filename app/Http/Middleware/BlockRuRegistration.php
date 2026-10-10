<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockRuRegistration
{
    protected array $blockedCountries = ['RU', 'BY'];

    protected array $blockedDomains = [
        'example.com', 'example.org', 'example.net',
        'test.com', 'test.test', 'fake.com', 'mailinator.com',
        'tempmail.com', 'guerrillamail.com', '10minutemail.com',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('POST') || ! $request->is('register')) {
            return $next($request);
        }

        // Cloudflare country check
        $country = strtoupper((string) $request->header('CF-IPCountry', ''));
        if (in_array($country, $this->blockedCountries, true)) {
            abort(403, 'Registration is not available from your region.');
        }

        // Email domain check
        $email = strtolower((string) $request->input('email', ''));
        if (preg_match('/\.(ru|by)$/i', $email)) {
            abort(403, 'Registration with this email domain is not available.');
        }

        $domain = substr(strrchr($email, '@'), 1);
        if (in_array($domain, $this->blockedDomains, true)) {
            // Разрешаем специальный тестовый домен Resend
            if ($domain === 'resend.dev') {
                return $next($request);
            }
            abort(403, 'Please use a real email address.');
        }

        return $next($request);
    }
}