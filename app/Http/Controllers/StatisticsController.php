<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function __invoke(Request $request)
    {
        $userId = $request->user()->id;

        $stats = [
            'comments'           => DB::table('comments')->where('user_id', $userId)->count(),
            'comment_reactions'  => DB::table('comment_reactions')->where('user_id', $userId)->count(),
            'projects_total'     => DB::table('user_projects')->where('user_id', $userId)->whereNull('deleted_at')->count(),
            'projects_approved'  => DB::table('user_projects')->where('user_id', $userId)->whereNull('deleted_at')->where('status', 'approved')->count(),
            'projects_pending'   => DB::table('user_projects')->where('user_id', $userId)->whereNull('deleted_at')->where('status', 'pending')->count(),
            'wiki_revisions'     => DB::table('wiki_revisions')->where('editor_id', $userId)->count(),
            'contact_messages'   => DB::table('contact_messages')->where('user_id', $userId)->count(),
            'sessions'           => DB::table('sessions')->where('user_id', $userId)->count(),
        ];

        $stats['projects_views'] = (int) DB::table('user_projects')
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->sum('views_count');

        return view('account.statistics', [
            'user'  => $request->user(),
            'stats' => $stats,
        ]);
    }
}
