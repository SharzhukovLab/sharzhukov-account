<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(Request $request): View
    {
        $me = $request->user();

        $conversations = Conversation::whereHas('users', fn($q) => $q->where('users.id', $me->id))
            ->with(['users', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->orderByDesc('last_message_at')
            ->get();

        return view('account.conversations.index', [
            'me'            => $me,
            'conversations' => $conversations,
        ]);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $me = $request->user();

        // Проверка участия
        abort_unless(
            $conversation->users()->where('users.id', $me->id)->exists(),
            403
        );

        // Помечаем прочитанным
        $conversation->markReadFor($me->id);

        $conversation->load(['messages.user', 'users']);

        $other = $conversation->users->firstWhere('id', '!=', $me->id);

        return view('account.conversations.show', [
            'me'           => $me,
            'conversation' => $conversation,
            'other'        => $other,
        ]);
    }

    public function store(Request $request, Conversation $conversation)
    {
        $me = $request->user();
        abort_unless(
            $conversation->users()->where('users.id', $me->id)->exists(),
            403
        );

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'user_id'         => $me->id,
            'body'            => $data['body'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Уведомить второго участника
        $other = $conversation->users->firstWhere('id', '!=', $me->id);
        if ($other) {
            try {
                $other->notify(new \App\Notifications\NewMessageNotification($me, $conversation, $data['body']));
            } catch (\Throwable $e) {
                \Log::warning('[dm] notify failed', ['err' => $e->getMessage()]);
            }
        }

        return redirect()
            ->route('conversations.show', $conversation)
            ->with('status', 'message-sent');
    }

    public function start(Request $request, string $username)
    {
        $me    = $request->user();
        $other = User::where('username', $username)->firstOrFail();

        if ($other->id === $me->id) {
            return redirect()->route('conversations.index')
                ->withErrors(['body' => "You can't message yourself."]);
        }

        $conv = Conversation::findOrCreateBetween($me->id, $other->id);

        return redirect()->route('conversations.show', $conv);
    }
}