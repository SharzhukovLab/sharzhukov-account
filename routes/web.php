<?php

use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/export-data', [ProfileController::class, 'exportData'])->name('account.export');
    Route::post('/request-deletion', [ProfileController::class, 'requestDeletion'])->name('account.deletion');

    Route::get('/security', SecurityController::class)->name('security');

    Route::get('/sessions', [SessionsController::class, 'index'])->name('sessions');
    Route::delete('/sessions/{id}', [SessionsController::class, 'destroy'])->name('sessions.destroy');

    Route::get('/messages', MessagesController::class)->name('messages');

    // ── Direct Messages ──
    Route::get('/conversations', [\App\Http\Controllers\ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/new/{username}', [\App\Http\Controllers\ConversationController::class, 'start'])->name('conversations.start');
    Route::get('/conversations/{conversation}', [\App\Http\Controllers\ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation}/messages', [\App\Http\Controllers\ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/statistics', StatisticsController::class)->name('statistics');

    Route::get('/projects', [ProjectsController::class, 'index'])->name('projects.index');
    Route::get('/projects/new', [ProjectsController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectsController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectsController::class, 'edit'])->name('projects.edit');
    Route::patch('/projects/{project}', [ProjectsController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectsController::class, 'destroy'])->name('projects.destroy');
});

Route::get('/logout', function (\Illuminate\Http\Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    $return = $request->input('return', 'https://sharzhukov.com');
    $host = parse_url($return, PHP_URL_HOST);

    if (! $host || ! preg_match('/(^|\.)sharzhukov\.com$/', $host)) {
        $return = 'https://sharzhukov.com';
    }

    return redirect()->away($return);
})->middleware('web')->name('logout.get');

Route::post('/api/cookie-consent', [CookieConsentController::class, 'store'])->name('cookie-consent.store');

Route::get('/logout-for-reset', function (\Illuminate\Http\Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('password.request');
})->middleware('web')->name('logout-for-reset');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/confirm/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// ─── SEO ───
Route::get('/robots.txt', fn () => response(
    "User-agent: *\nDisallow: /\n\nSitemap: https://main.sharzhukov.com/sitemap.xml\n",
    200,
    ['Content-Type' => 'text/plain']
))->name('robots');

// ── Notifications ──
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationsController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications/recent', [\App\Http\Controllers\NotificationsController::class, 'recent'])->name('notifications.recent');
    Route::get('/api/notifications/unread-count', [\App\Http\Controllers\NotificationsController::class, 'unreadCount'])->name('notifications.unread');
    Route::post('/api/notifications/{id}/read', [\App\Http\Controllers\NotificationsController::class, 'markRead'])->name('notifications.read');
    Route::post('/api/notifications/mark-all-read', [\App\Http\Controllers\NotificationsController::class, 'markAllRead'])->name('notifications.mark-all');
    Route::delete('/api/notifications/{id}', [\App\Http\Controllers\NotificationsController::class, 'destroy'])->name('notifications.destroy');
});
