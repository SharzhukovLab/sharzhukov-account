<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CaptureReturnUrl
{
    public function handle(Request $request, Closure $next)
    {
        $return = $request->input('return');

        if ($return && is_string($return)) {
            $host = parse_url($return, PHP_URL_HOST);

            if ($host && preg_match('/(^|\.)sharzhukov\.com$/', $host)) {
                $request->session()->put('auth.return_url', $return);
            }
        }

        return $next($request);
    }
}
