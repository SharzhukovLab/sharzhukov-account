<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('account.profile', [
            'user' => $request->user(),
            'returnUrl' => $request->input('return') ?? session('auth.return_url'),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $specTypes = array_keys(User::specTypes());

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:80'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9\-]+$/', Rule::unique('users')->ignore($user->id)],
            'bio'      => ['nullable', 'string', 'max:500'],
            'avatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'social_links' => ['nullable', 'array', 'max:10'],
            'social_links.*.platform' => ['required_with:social_links.*.url', 'string', 'in:' . implode(',', array_keys(User::allowedSocialPlatforms()))],
            'social_links.*.url'      => ['required_with:social_links.*.platform', 'url', 'max:500'],
            'team_specs_data'                              => ['nullable', 'array', 'max:10'],
            'team_specs_data.*.name'                       => ['nullable', 'string', 'max:60'],
            'team_specs_data.*.components'                 => ['nullable', 'array', 'max:20'],
            'team_specs_data.*.components.*.type'          => ['nullable', 'string', 'in:' . implode(',', $specTypes)],
            'team_specs_data.*.components.*.value'         => ['nullable', 'string', 'max:120'],
        ], [
            'username.regex' => 'Username may only contain lowercase letters, numbers, and dashes.',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        $links = [];
        foreach (($data['social_links'] ?? []) as $link) {
            if (!empty($link['platform']) && !empty($link['url'])) {
                $links[] = ['platform' => $link['platform'], 'url' => $link['url']];
            }
        }
        $data['social_links'] = $links;

        // ── Nested specs: [{name, components:[{type,value}]}] ──
        $blocks = [];
        foreach (($data['team_specs_data'] ?? []) as $block) {
            if (!is_array($block)) continue;
            $name = trim((string) ($block['name'] ?? ''));
            $comps = is_array($block['components'] ?? null) ? $block['components'] : [];
            $items = [];
            foreach ($comps as $c) {
                if (!is_array($c)) continue;
                $type = (string) ($c['type'] ?? '');
                $val  = trim((string) ($c['value'] ?? ''));
                if ($type === '' || $val === '') continue;
                $items[] = ['type' => $type, 'value' => $val];
            }
            if ($name === '' && empty($items)) continue;
            $blocks[] = [
                'name'       => $name !== '' ? $name : 'Device',
                'components' => $items,
            ];
        }
        $data['team_specs_data'] = $blocks;
        if (!empty($blocks)) {
            $data['team_specs'] = null;
        }

        unset($data['avatar']);
        $user->update($data);

        return back()->with('status', 'profile-updated');
    }

    public function exportData(Request $request)
    {
        $user = $request->user();

        $data = [
            'exported_at' => now()->toIso8601String(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'bio' => $user->bio,
                'social_links' => $user->social_links,
                'team_specs_data' => $user->team_specs_data,
                'created_at' => $user->created_at,
                'email_verified_at' => $user->email_verified_at,
            ],
        ];

        $filename = 'sharzhukov-lab-data-' . $user->username . '-' . now()->format('Y-m-d') . '.json';

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function requestDeletion(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password' => ['required', 'current_password'],
            'confirm'  => ['required', 'accepted'],
        ]);

        $user->update(['deletion_requested_at' => now()]);

        return redirect()->route('dashboard')
            ->with('status', 'Account deletion scheduled. Your account will be removed within 30 days.');
    }
}
