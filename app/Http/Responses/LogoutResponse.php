<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        $return = $request->input('return', 'https://sharzhukov.com');
        $host = parse_url($return, PHP_URL_HOST);

        if (! $host || ! preg_match('/(^|\.)sharzhukov\.com$/', $host)) {
            $return = 'https://sharzhukov.com';
        }

        return redirect()->away($return);
    }
}
