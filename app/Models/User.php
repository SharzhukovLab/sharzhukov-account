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
        if ($this->avatar_path) {
            return 'https://main.sharzhukov.com/storage/' . ltrim($this->avatar_path, '/');
        }

        $initial = strtoupper(mb_substr($this->name ?? 'U', 0, 1));
        $initial = htmlspecialchars($initial, ENT_XML1, 'UTF-8');

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128">'
             . '<rect width="128" height="128" fill="#ff5c1f"/>'
             . '<text x="64" y="84" font-family="-apple-system,BlinkMacSystemFont,sans-serif" '
             . 'font-size="60" font-weight="900" fill="#ffffff" text-anchor="middle" '
             . 'letter-spacing="-2">' . $initial . '</text>'
             . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
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
            'desktop'  => ['label' => 'Desktop',  'icon' => '🖥'],
            'laptop'   => ['label' => 'Laptop',   'icon' => '💻'],
            'cpu'      => ['label' => 'CPU',      'icon' => '⚙️'],
            'gpu'      => ['label' => 'GPU',      'icon' => '🎮'],
            'ram'      => ['label' => 'RAM',      'icon' => '🧠'],
            'storage'  => ['label' => 'Storage',  'icon' => '💾'],
            'os'       => ['label' => 'OS',       'icon' => '🪟'],
            'display'  => ['label' => 'Display',  'icon' => '📺'],
            'keyboard' => ['label' => 'Keyboard', 'icon' => '⌨️'],
            'mouse'    => ['label' => 'Mouse',    'icon' => '🖱'],
            'custom'   => ['label' => 'Other',    'icon' => '✨'],
        ];
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
}
