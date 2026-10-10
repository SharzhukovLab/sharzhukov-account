<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SessionsController extends Controller
{
    public function index(Request $request)
    {
        $sessions = DB::table('sessions')
            ->where('user_id', auth()->id())
            ->orderByDesc('last_activity')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'ip' => $s->ip_address,
                'ua' => $s->user_agent,
                'last_active' => Carbon::createFromTimestamp((int) $s->last_activity),
                'is_current' => $s->id === session()->getId(),
            ]);

        return view('account.sessions', ['sessions' => $sessions]);
    }

    public function destroy(Request $request, string $id)
    {
        if ($id === session()->getId()) {
            return back()->with('error', 'Нельзя удалить текущую сессию.');
        }

        DB::table('sessions')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->delete();

        return back()->with('status', 'Сессия завершена.');
    }
}
