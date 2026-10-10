<x-layouts.app :title="($project->exists ? 'Edit project' : 'New project') . ' — Sharzhukov Account'">
    <div class="page-header">
        <span class="lab-eyebrow">// {{ $project->exists ? 'Edit' : 'New' }} project</span>
        <h1>{{ $project->exists ? 'Edit project' : 'Submit a project' }}</h1>
        <p>
            @if (!$project->exists)
                All projects go through moderation before appearing publicly.
            @elseif ($project->status === 'approved')
                Editing will reset the project to <strong>pending</strong> for re-review.
            @endif
        </p>
    </div>

    @if ($errors->any())
        <x-lab::alert variant="danger" style="margin-bottom: 16px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-lab::alert>
    @endif

    <form method="POST"
          action="{{ $project->exists ? route('projects.update', $project) : route('projects.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if ($project->exists)
            @method('PATCH')
        @endif

        <x-lab::card style="margin-bottom: 16px;">
            <div class="field">
                <label class="lab-label">Cover image</label>
                @if ($project->cover_image_url)
                    <img src="{{ $project->cover_image_url }}" alt="" style="max-width: 300px; border-radius: 12px; margin-bottom: 10px; display: block;">
                @endif
                <input type="file" name="cover_image" accept="image/*" style="padding: 10px; border: 1.5px dashed var(--lab-border); border-radius: 12px; width: 100%; background: var(--lab-bg-elevated);">
                <div class="field-hint">JPG, PNG, WebP. Max 4 MB.</div>
            </div>

            <div class="field">
                <label for="title" class="lab-label">Title *</label>
                <input id="title" type="text" name="title" value="{{ old('title', $project->title) }}" required maxlength="200" class="lab-input">
            </div>

            <div class="field">
                <label for="tagline" class="lab-label">Tagline</label>
                <input id="tagline" type="text" name="tagline" value="{{ old('tagline', $project->tagline) }}" maxlength="255" placeholder="One-line description." class="lab-input">
            </div>

            <div class="field">
                <label for="description" class="lab-label">Description</label>
                <textarea id="description" name="description" rows="8" maxlength="30000" placeholder="Full description — what it does, why you built it, etc." class="lab-textarea">{{ old('description', $project->description) }}</textarea>
                <div class="field-hint">Basic HTML supported: &lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;ul&gt;, &lt;a&gt;.</div>
            </div>
        </x-lab::card>

        <x-lab::card style="margin-bottom: 16px;">
            <h2 style="margin-top: 0;">Links</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="field" style="margin: 0;">
                    <label class="lab-label">GitHub</label>
                    <input type="url" name="github_url" value="{{ old('github_url', $project->github_url) }}" placeholder="https://github.com/..." class="lab-input">
                </div>
                <div class="field" style="margin: 0;">
                    <label class="lab-label">Live demo</label>
                    <input type="url" name="demo_url" value="{{ old('demo_url', $project->demo_url) }}" placeholder="https://..." class="lab-input">
                </div>
                <div class="field" style="margin: 0;">
                    <label class="lab-label">Download</label>
                    <input type="url" name="download_url" value="{{ old('download_url', $project->download_url) }}" placeholder="https://..." class="lab-input">
                </div>
                <div class="field" style="margin: 0;">
                    <label class="lab-label">Other link</label>
                    <input type="url" name="external_url" value="{{ old('external_url', $project->external_url) }}" placeholder="https://..." class="lab-input">
                </div>
            </div>
        </x-lab::card>

        <x-lab::card style="margin-bottom: 16px;">
            <h2 style="margin-top: 0;">Tech stack</h2>
            <div id="tech-stack-editor" style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px;">
                @php $existing = old('tech_stack', $project->tech_stack ?? []); @endphp
                @foreach ($existing as $tech)
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; background: var(--lab-bg-elevated); border: 1px solid var(--lab-border); border-radius: 8px; font-size: 13px;" data-tech="{{ $tech }}">
                        {{ $tech }}
                        <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; color: var(--lab-fg-muted); font-size: 14px;">&times;</button>
                        <input type="hidden" name="tech_stack[]" value="{{ $tech }}">
                    </span>
                @endforeach
            </div>
            <input type="text" id="tech-stack-input" placeholder="Type and press Enter&hellip;" maxlength="40" class="lab-input">
            <div class="field-hint">Enter or comma to add. Max 15.</div>
        </x-lab::card>

        <x-lab::card style="margin-bottom: 16px;">
            <h2 style="margin-top: 0;">Metadata</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="field" style="margin: 0;">
                    <label for="license" class="lab-label">License</label>
                    <input id="license" type="text" name="license" value="{{ old('license', $project->license) }}" maxlength="80" placeholder="MIT, GPL, Apache 2.0…" class="lab-input">
                </div>
                <div class="field" style="margin: 0;">
                    <label for="status_label" class="lab-label">Status</label>
                    <select id="status_label" name="status_label" class="lab-select">
                        <option value="">— Select —</option>
                        @foreach (['Stable', 'Beta', 'Alpha', 'WIP', 'Deprecated'] as $s)
                            <option value="{{ $s }}" {{ old('status_label', $project->status_label) === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-lab::card>

        <x-lab::card style="margin-bottom: 16px;">
            <h2 style="margin-top: 0;">Gallery</h2>
            @if (!empty($project->gallery_urls))
                <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px;">
                    @foreach ($project->gallery_urls as $url)
                        <img src="{{ $url }}" alt="" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px;">
                    @endforeach
                </div>
            @endif
            <input type="file" name="gallery[]" accept="image/*" multiple style="padding: 10px; border: 1.5px dashed var(--lab-border); border-radius: 12px; width: 100%; background: var(--lab-bg-elevated);">
            <div class="field-hint">Up to 8 images. Each up to 4 MB. New ones are appended.</div>
        </x-lab::card>

        <div style="display: flex; gap: 12px;">
            <x-lab::button variant="secondary" :href="route('projects.index')">Cancel</x-lab::button>
            <x-lab::button variant="primary" type="submit">{{ $project->exists ? 'Save changes' : 'Submit for review' }}</x-lab::button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const editor = document.getElementById('tech-stack-editor');
            const input = document.getElementById('tech-stack-input');
            if (!editor || !input) return;
            const MAX = 15;

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    const value = input.value.trim();
                    if (!value) return;
                    if (editor.querySelectorAll('[data-tech]').length >= MAX) return;
                    addTag(value);
                    input.value = '';
                }
            });

            function addTag(value) {
                const span = document.createElement('span');
                span.setAttribute('data-tech', value);
                span.style.cssText = 'display:inline-flex;align-items:center;gap:6px;padding:5px 10px;background:var(--lab-bg-elevated);border:1px solid var(--lab-border);border-radius:8px;font-size:13px;';
                span.innerHTML = `
                    ${value}
                    <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:var(--lab-fg-muted);font-size:14px;">&times;</button>
                    <input type="hidden" name="tech_stack[]" value="${value.replace(/"/g, '&quot;')}">
                `;
                editor.appendChild(span);
            }
        });
    </script>
</x-layouts.app>