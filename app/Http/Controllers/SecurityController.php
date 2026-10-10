<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('account.security', [
            'user' => $request->user(),
        ]);
    }
}
