<x-layouts.app title="My account — Sharzhukov Account">
    <div class="page-header">
        <span class="lab-eyebrow">// Profile</span>
        <h1>My account</h1>
        <p>Manage your profile, links and hardware specs.</p>
    </div>

    @if (session('status') === 'profile-updated')
        <x-lab::alert variant="success" style="margin-bottom: 16px;">Profile updated.</x-lab::alert>
    @endif

    @if ($errors->any())
        <x-lab::alert variant="danger" style="margin-bottom: 16px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-lab::alert>
    @endif

    <x-lab::card>
        <h2 style="margin-top: 0;">Profile information</h2>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="avatar-row">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                <div>
                    <label class="avatar-upload-btn">
                        <input type="file" name="avatar" accept="image/*" style="display: none;" onchange="this.closest('.avatar-row').querySelector('.avatar-filename').textContent = this.files[0]?.name || ''">
                        Change photo
                    </label>
                    <span class="avatar-filename"></span>
                    <div class="field-hint">JPG, PNG, WebP. Max 2 MB.</div>
                </div>
            </div>

            <div class="field">
                <label for="name" class="lab-label">Full name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="lab-input">
            </div>

            <div class="field">
                <label for="username" class="lab-label">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}" required class="lab-input">
                <div class="field-hint">Your public profile: sharzhukov.com/u/<strong>{{ $user->username }}</strong></div>
            </div>

            <div class="field">
                <label for="bio" class="lab-label">Short bio</label>
                <textarea id="bio" name="bio" rows="3" maxlength="500" class="lab-textarea">{{ old('bio', $user->bio) }}</textarea>
                <div class="field-hint">Max 500 characters.</div>
            </div>

            <div class="field">
                <label class="lab-label">Social links</label>
                <div class="field-hint" style="margin: 0 0 10px;">Shown on your public profile.</div>

                <div id="social-links-list" class="editor-list">
                    @php
                        $platforms = \App\Models\User::allowedSocialPlatforms();
                        $existing = old('social_links', $user->social_links ?? []);
                    @endphp
                    @foreach ($existing as $i => $link)
                        <div class="editor-row">
                            <select name="social_links[{{ $i }}][platform]">
                                @foreach ($platforms as $key => $label)
                                    <option value="{{ $key }}" {{ ($link['platform'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <input type="url" name="social_links[{{ $i }}][url]" value="{{ $link['url'] ?? '' }}" placeholder="https://...">
                            <button type="button" class="row-remove" onclick="this.parentElement.remove()">&times;</button>
                        </div>
                    @endforeach
                </div>

                <x-lab::button variant="secondary" size="sm" type="button" onclick="addSocialLink()">+ Add link</x-lab::button>
            </div>

            <div class="field">
                <label class="lab-label">Hardware specs</label>
                <div class="field-hint" style="margin: 0 0 10px;">Shown on your public profile.</div>

                <div id="specs-list" class="editor-list">
                    @php
                        $specTypes = \App\Models\User::specTypes();
                        $existingSpecs = old('team_specs_data', $user->team_specs_data ?? []);
                    @endphp
                    @foreach ($existingSpecs as $i => $spec)
                        <div class="editor-row">
                            <select name="team_specs_data[{{ $i }}][type]">
                                @foreach ($specTypes as $key => $meta)
                                    <option value="{{ $key }}" {{ ($spec['type'] ?? '') === $key ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="team_specs_data[{{ $i }}][value]" value="{{ $spec['value'] ?? '' }}" placeholder="e.g. Intel Core Ultra 9">
                            <button type="button" class="row-remove" onclick="this.parentElement.remove()">&times;</button>
                        </div>
                    @endforeach
                </div>

                <x-lab::button variant="secondary" size="sm" type="button" onclick="addSpec()">+ Add component</x-lab::button>
            </div>

            <div class="field">
                <label class="lab-label">Email</label>
                <input type="email" value="{{ $user->email }}" disabled class="lab-input" style="opacity: 0.6;">
                <div class="field-hint">
                    @if ($user->hasVerifiedEmail())
                        <span style="color: #10b981;">&#10003; Verified</span>
                    @else
                        <span style="color: #ea580c;">Not verified</span>
                    @endif
                </div>
            </div>

            <x-lab::button variant="primary" type="submit">Save changes</x-lab::button>
        </form>
    </x-lab::card>

    @if ($returnUrl)
        <x-lab::card style="margin-top: 16px; text-align: center;">
            <p style="color: var(--lab-fg-muted); font-size: 14px; margin-bottom: 14px;">
                Return to the site you came from?
            </p>
            <x-lab::button variant="secondary" :href="$returnUrl">&larr; Back to {{ parse_url($returnUrl, PHP_URL_HOST) }}</x-lab::button>
        </x-lab::card>
    @endif

    <script>
        const PLATFORMS = @json(\App\Models\User::allowedSocialPlatforms());
        const SPEC_TYPES = @json(\App\Models\User::specTypes());

        function addSocialLink() {
            const list = document.getElementById('social-links-list');
            const idx = list.children.length + 100;
            const row = document.createElement('div');
            row.className = 'editor-row';
            row.innerHTML = `
                <select name="social_links[${idx}][platform]">
                    ${Object.entries(PLATFORMS).map(([k, v]) => `<option value="${k}">${v}</option>`).join('')}
                </select>
                <input type="url" name="social_links[${idx}][url]" placeholder="https://...">
                <button type="button" class="row-remove" onclick="this.parentElement.remove()">&times;</button>
            `;
            list.appendChild(row);
            row.querySelector('input').focus();
        }

        function addSpec() {
            const list = document.getElementById('specs-list');
            const idx = list.children.length + 100;
            const row = document.createElement('div');
            row.className = 'editor-row';
            row.innerHTML = `
                <select name="team_specs_data[${idx}][type]">
                    ${Object.entries(SPEC_TYPES).map(([k, v]) => `<option value="${k}">${v.label}</option>`).join('')}
                </select>
                <input type="text" name="team_specs_data[${idx}][value]" placeholder="e.g. Intel Core Ultra 9">
                <button type="button" class="row-remove" onclick="this.parentElement.remove()">&times;</button>
            `;
            list.appendChild(row);
            row.querySelector('input').focus();
        }
    </script>
</x-layouts.app>