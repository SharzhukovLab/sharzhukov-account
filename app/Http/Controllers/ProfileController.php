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
            'team_specs_data'              => ['nullable', 'array', 'max:15'],
            'team_specs_data.*.type'       => ['required_with:team_specs_data.*.value', 'string', 'in:' . implode(',', $specTypes)],
            'team_specs_data.*.value'      => ['required_with:team_specs_data.*.type', 'string', 'max:120'],
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

        $specs = [];
        foreach (($data['team_specs_data'] ?? []) as $spec) {
            if (!empty($spec['type']) && !empty($spec['value'])) {
                $specs[] = ['type' => $spec['type'], 'value' => trim($spec['value'])];
            }
        }
        $data['team_specs_data'] = $specs;
        if (!empty($specs)) {
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
