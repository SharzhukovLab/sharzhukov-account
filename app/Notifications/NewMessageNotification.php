<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $from,
        public Conversation $conversation,
        public string $body,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'            => 'new_message',
            'title'           => 'New message from ' . $this->from->name,
            'message'         => Str::limit($this->body, 120),
            'url'             => '/conversations/' . $this->conversation->id,
            'conversation_id' => $this->conversation->id,
            'from_username'   => $this->from->username,
        ];
    }
}