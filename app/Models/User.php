<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Passkeys\PasskeyAuthenticatable;
use Laravel\Passkeys\Contracts\PasskeyUser;

class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, PasskeyAuthenticatable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar_path',
        'bio',
        'social_links',
        'terms_accepted_at',
        'deletion_requested_at',
        'team_role',
        'team_roles_data',
        'team_year',
        'team_specs',
        'team_specs_data',
        'team_order',
        'show_in_team',
        'approved_comments_count',
        'is_trusted',
        'is_banned',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at'       => 'datetime',
        'password'                => 'hashed',
        'terms_accepted_at'       => 'datetime',
        'deletion_requested_at'   => 'datetime',
        'is_trusted'              => 'boolean',
        'is_banned'               => 'boolean',
        'show_in_team'            => 'boolean',
        'team_specs_data'         => 'array',
        'team_roles_data'         => 'array',
        'social_links'            => 'array',
        'two_factor_confirmed_at' => 'datetime',
    ];

/**
     * Avatar URL — real file or generated SVG placeholder with initial.
     */
        /**
     * Avatar URL — real file or generated SVG placeholder with initial.
     */
    public function getAvatarUrlAttribute(): string
    {
        // 1) Загруженный файл
        if (!empty($this->avatar_path)) {
            return 'https://sharzhukov.com/storage/' . ltrim($this->avatar_path, '/');
        }
        // 2) Внешний (Google и т.п.)
        if (!empty($this->avatar_url_external)) {
            return $this->avatar_url_external;
        }
        // 3) Заглушка — пустая (шаблон отрендерит fallback с инициалом)
        return '';
    }

    public function getProfileUrlAttribute(): string
    {
        return 'https://sharzhukov.com/u/' . ($this->username ?: $this->id);
    }

    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (empty($user->username)) {
                $base = Str::slug($user->name ?: explode('@', $user->email)[0], '-');
                $user->username = $base . '-' . Str::lower(Str::random(4));
            }
        });
    }

    public static function allowedSocialPlatforms(): array
    {
        return [
            'github'   => 'GitHub',
            'twitter'  => 'X / Twitter',
            'telegram' => 'Telegram',
            'discord'  => 'Discord',
            'reddit'   => 'Reddit',
            'youtube'  => 'YouTube',
            'twitch'   => 'Twitch',
            'vk'       => 'VK',
            'linkedin' => 'LinkedIn',
            'website'  => 'Personal website',
        ];
    }

        public static function specTypes(): array
    {
        return [
            'cpu'         => ['label' => 'CPU'],
            'gpu'         => ['label' => 'GPU'],
            'ram'         => ['label' => 'RAM'],
            'storage'     => ['label' => 'Storage'],
            'motherboard' => ['label' => 'Motherboard'],
            'psu'         => ['label' => 'Power Supply'],
            'case'        => ['label' => 'Case'],
            'cooling'     => ['label' => 'Cooling'],
            'os'          => ['label' => 'OS'],
            'display'     => ['label' => 'Display'],
            'keyboard'    => ['label' => 'Keyboard'],
            'mouse'       => ['label' => 'Mouse'],
            'audio'       => ['label' => 'Audio'],
            'network'     => ['label' => 'Network'],
            'custom'      => ['label' => 'Other'],
        ];
    }

    public static function isNestedSpecs(array $specs): bool
    {
        if (empty($specs)) return true;
        $first = reset($specs);
        return is_array($first) && isset($first['components']) && is_array($first['components']);
    }

public function projects()
{
    return $this->hasMany(UserProject::class);
}

public function comments()
{
    return $this->hasMany(Comment::class);
}

    public function sendEmailVerificationNotification(): void
    {
        // Используем дефолтное уведомление Laravel
        if ($this instanceof MustVerifyEmail && ! $this->hasVerifiedEmail()) {
            $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
        }
    }

    // ── Direct Messages ──
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')
            ->withPivot('last_read_at')
            ->orderByDesc('last_message_at');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getUnreadMessagesCountAttribute(): int
    {
        return Conversation::whereHas('users', fn($q) => $q->where('users.id', $this->id))
            ->get()
            ->sum(fn($c) => $c->unreadCountFor($this->id));
    }
}
