<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $sessionsCount = DB::table('sessions')
            ->where('user_id', auth()->id())
            ->count();

        return view('account.dashboard', [
            'user' => $request->user(),
            'sessionsCount' => $sessionsCount,
        ]);
    }
}
