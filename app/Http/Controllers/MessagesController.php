<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $messages = ContactMessage::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->orderByDesc('created_at')
            ->get();

        return view('account.messages', [
            'messages' => $messages,
        ]);
    }
}
