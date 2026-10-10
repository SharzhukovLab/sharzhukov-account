<?php

use App\Http\Middleware\BlockRuRegistration;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(at: '*');

    $middleware->redirectGuestsTo(fn () => route('login'));

    $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
        $return = $request->input('return');
        if ($return) {
            $host = parse_url($return, PHP_URL_HOST);
            if ($host && preg_match('/(^|\.)sharzhukov\.com$/', $host)) {
                return $return;
            }
        }

        return url('/dashboard');
    });

    $middleware->web(append: [
        \App\Http\Middleware\BlockRuRegistration::class,
        \App\Http\Middleware\CaptureReturnUrl::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
