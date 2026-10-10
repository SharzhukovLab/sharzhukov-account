<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name', 'email', 'subject', 'message',
        'category', 'role_interest',
        'ip_address', 'user_agent', 'user_id',
        'status', 'admin_note', 'reply_body', 'read_at', 'replied_at',
    ];

    protected $casts = [
        'read_at'    => 'datetime',
        'replied_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function scopeNew($q) { return $q->where('status', 'new'); }

    /**
     * Категории обращений.
     */
    public static function categories(): array
    {
        return [
            'general'     => ['label' => 'General message',       'icon' => 'chat',    'desc' => 'Questions, feedback, or anything else.'],
            'join_team'   => ['label' => 'Join the team',         'icon' => 'user-plus','desc' => 'Want to contribute as a developer, designer, editor?'],
            'suggestion'  => ['label' => 'Suggestion',            'icon' => 'lightbulb', 'desc' => 'Have an idea to improve our service?'],
            'complaint'   => ['label' => 'Complaint',             'icon' => 'alert',   'desc' => 'Something went wrong — tell us about it.'],
            'bug'         => ['label' => 'Bug report',            'icon' => 'bug',     'desc' => 'Found a technical issue on the site?'],
            'partnership' => ['label' => 'Partnership',           'icon' => 'handshake','desc' => 'Business, sponsorship, or collaboration.'],
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categories()[$this->category]['label'] ?? 'Message';
    }

    public function markAsRead(): void
    {
        if ($this->status === 'new') {
            $this->update(['status' => 'read', 'read_at' => now()]);
        }
    }
}
