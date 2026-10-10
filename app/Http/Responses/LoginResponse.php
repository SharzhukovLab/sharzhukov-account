<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $inputReturn = $request->input('return');
        $sessionReturn = session()->pull('auth.return_url');

        $return = $inputReturn
            ?: $sessionReturn
            ?: 'https://sharzhukov.com';

        if (! $this->isAllowed($return)) {
            $return = 'https://sharzhukov.com';
        }

        Log::info('LoginResponse', [
            'input_return'   => $inputReturn,
            'session_return' => $sessionReturn,
            'final'          => $return,
            'referer'        => $request->headers->get('referer'),
        ]);

        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->away($return);
    }

    private function isAllowed(?string $url): bool
    {
        if (! $url) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return $host && preg_match('/(^|\.)sharzhukov\.com$/', $host);
    }
}
