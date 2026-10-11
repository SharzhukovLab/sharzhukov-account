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
                <div class="field-hint" style="margin: 0 0 14px;">Shown on your public profile. Group components by device.</div>

                @php
                    $specTypes = \App\Models\User::specTypes();
                    $existingBlocks = old('team_specs_data', $user->team_specs_data ?? []);

                    // Legacy flat → wrap into single device
                    if (!empty($existingBlocks) && is_array($existingBlocks)) {
                        $first = reset($existingBlocks);
                        if (is_array($first) && !isset($first['components']) && isset($first['type'])) {
                            $existingBlocks = [['name' => 'Main PC', 'components' => $existingBlocks]];
                        }
                    }
                    if (empty($existingBlocks)) {
                        $existingBlocks = [['name' => 'Main PC', 'components' => []]];
                    }
                @endphp

                <div id="specs-editor" data-spec-types='@json($specTypes)'>

                    <div id="specs-blocks">
                        @foreach ($existingBlocks as $bi => $block)
                            <div class="specs-block">
                                <div class="specs-block-head">
                                    <input type="text"
                                           name="team_specs_data[{{ $bi }}][name]"
                                           value="{{ $block['name'] ?? '' }}"
                                           placeholder="Device name (e.g. Main PC)"
                                           class="specs-block-name">
                                    <button type="button" class="specs-block-remove"
                                            onclick="this.closest('.specs-block').remove()">&times;</button>
                                </div>

                                <div class="specs-components">
                                    @foreach (($block['components'] ?? []) as $ci => $comp)
                                        <div class="specs-component">
                                            <select name="team_specs_data[{{ $bi }}][components][{{ $ci }}][type]">
                                                @foreach ($specTypes as $key => $meta)
                                                    <option value="{{ $key }}" {{ ($comp['type'] ?? '') === $key ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text"
                                                   name="team_specs_data[{{ $bi }}][components][{{ $ci }}][value]"
                                                   value="{{ $comp['value'] ?? '' }}"
                                                   placeholder="e.g. Intel Core i9">
                                            <button type="button" class="row-remove"
                                                    onclick="this.closest('.specs-component').remove()">&times;</button>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" class="lab-btn lab-btn--secondary specs-add-component"
                                        onclick="specsAddComponent(this)">+ Add component</button>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="lab-btn lab-btn--secondary" onclick="specsAddBlock()" style="margin-top:12px;">
                        + Add device
                    </button>
                </div>
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

        // ── Hardware specs: nested editor ──
        function specsAddComponent(btn) {
            var editor = document.getElementById('specs-editor');
            if (!editor) return;
            var SPEC_TYPES = JSON.parse(editor.dataset.specTypes);
            var block = btn.closest('.specs-block');
            var blockEl = block;
            var blockIdx = Array.prototype.indexOf.call(
                document.getElementById('specs-blocks').children, blockEl
            );
            var container = block.querySelector('.specs-components');
            var cIdx = container.querySelectorAll('.specs-component').length;

            var opts = Object.entries(SPEC_TYPES)
                .map(function (e) { return '<option value="' + e[0] + '">' + e[1].label + '</option>'; })
                .join('');

            var div = document.createElement('div');
            div.className = 'specs-component';
            div.innerHTML =
                '<select name="team_specs_data[' + blockIdx + '][components][' + cIdx + '][type]">' + opts + '</select>' +
                '<input type="text" name="team_specs_data[' + blockIdx + '][components][' + cIdx + '][value]" placeholder="e.g. Intel Core i9">' +
                '<button type="button" class="row-remove" onclick="this.closest(\'.specs-component\').remove()">&times;</button>';
            container.appendChild(div);
            div.querySelector('input').focus();
        }

        function specsAddBlock() {
            var editor = document.getElementById('specs-editor');
            if (!editor) return;
            var SPEC_TYPES = JSON.parse(editor.dataset.specTypes);
            var blockList = document.getElementById('specs-blocks');
            var idx = blockList.querySelectorAll('.specs-block').length;

            var opts = Object.entries(SPEC_TYPES)
                .map(function (e) { return '<option value="' + e[0] + '">' + e[1].label + '</option>'; })
                .join('');

            var wrapper = document.createElement('div');
            wrapper.className = 'specs-block';
            wrapper.innerHTML =
                '<div class="specs-block-head">' +
                    '<input type="text" name="team_specs_data[' + idx + '][name]" value="" placeholder="Device name (e.g. Laptop)" class="specs-block-name">' +
                    '<button type="button" class="specs-block-remove" onclick="this.closest(\'.specs-block\').remove()">&times;</button>' +
                '</div>' +
                '<div class="specs-components"></div>' +
                '<button type="button" class="lab-btn lab-btn--secondary specs-add-component" onclick="specsAddComponent(this)">+ Add component</button>';

            blockList.appendChild(wrapper);
            wrapper.querySelector('.specs-block-name').focus();
        }
    </script>

    {{-- Hardware specs editor styles --}}
    <style>
        .specs-block {
            border: 2px solid var(--brutal-border);
            padding: 14px;
            margin-bottom: 12px;
            background: var(--lab-bg);
        }
        .specs-block-head {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
            align-items: center;
        }
        .specs-block-name {
            flex: 1;
            height: 38px;
            padding: 0 12px;
            border: 2px solid var(--brutal-border);
            background: var(--lab-bg);
            color: var(--lab-fg);
            font-family: var(--font-mono);
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .specs-block-remove {
            background: transparent;
            border: 2px solid var(--brutal-border);
            width: 38px;
            height: 38px;
            cursor: pointer;
            color: var(--brutal-accent);
            font-size: 20px;
            line-height: 1;
            flex-shrink: 0;
        }
        .specs-block-remove:hover { background: var(--brutal-accent); color: #fff; }
        .specs-component {
            display: flex;
            gap: 8px;
            margin-bottom: 6px;
            align-items: center;
        }
        .specs-component select {
            min-width: 140px;
            height: 36px;
            padding: 0 10px;
            border: 2px solid var(--brutal-border);
            background: var(--lab-bg);
            color: var(--lab-fg);
            font-family: var(--font-mono);
            font-size: 12px;
        }
        .specs-component input {
            flex: 1;
            height: 36px;
            padding: 0 12px;
            border: 2px solid var(--brutal-border);
            background: var(--lab-bg);
            color: var(--lab-fg);
            font-family: var(--font-mono);
            font-size: 13px;
        }
        .specs-component .row-remove {
            background: transparent;
            border: 2px solid var(--brutal-border);
            width: 36px;
            height: 36px;
            cursor: pointer;
            color: var(--brutal-accent);
            font-size: 18px;
            line-height: 1;
            flex-shrink: 0;
        }
        .specs-component .row-remove:hover { background: var(--brutal-accent); color: #fff; }
        .specs-add-component {
            font-size: 11px !important;
            padding: 6px 12px !important;
            margin-top: 4px;
        }
    </style>
</x-layouts.app>