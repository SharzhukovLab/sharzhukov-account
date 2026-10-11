<x-layouts.app title="My projects — Sharzhukov Account">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-end; gap: 16px;">
        <div>
            <span class="lab-eyebrow">// Projects</span>
            <h1 style="margin-bottom: 0;">My projects</h1>
            <p>Submit and manage your open-source projects.</p>
        </div>
        <x-lab::button variant="primary" :href="route('projects.create')">+ New project</x-lab::button>
    </div>

    @if (session('status'))
        <x-lab::alert variant="success" style="margin-bottom: 16px;">{{ session('status') }}</x-lab::alert>
    @endif

    @if ($projects->isEmpty())
        <x-lab::card style="text-align: center; padding: 60px 40px;">
            <p style="color: var(--lab-fg-muted); margin-bottom: 20px;">You haven't submitted any projects yet.</p>
            <x-lab::button variant="primary" :href="route('projects.create')">Submit your first project</x-lab::button>
        </x-lab::card>
    @else
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach ($projects as $project)
                <x-lab::card>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                        <div style="flex: 1; min-width: 0;">
                            <strong style="font-size: 16px; font-weight: 600; color: var(--lab-fg);">{{ $project->title }}</strong>
                            @if ($project->tagline)
                                <p style="color: var(--lab-fg-muted); font-size: 13px; margin: 4px 0 0;">{{ $project->tagline }}</p>
                            @endif
                            <div style="margin-top: 10px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                @switch($project->status)
                                    @case('pending')
                                        <x-lab::badge variant="warning">Pending review</x-lab::badge>
                                        @break
                                    @case('approved')
                                        <x-lab::badge variant="success">Live</x-lab::badge>
                                        @break
                                    @case('rejected')
                                        <x-lab::badge variant="danger">Rejected</x-lab::badge>
                                        @break
                                    @default
                                        <x-lab::badge>{{ ucfirst($project->status) }}</x-lab::badge>
                                @endswitch
                                <span style="font-size: 12px; color: var(--lab-fg-muted);">{{ $project->views_count }} views</span>
                                <span style="font-size: 12px; color: var(--lab-fg-subtle);">&middot; {{ $project->created_at->format('M d, Y') }}</span>
                            </div>
                            @if ($project->status === 'rejected' && $project->moderation_note)
                                <x-lab::alert variant="danger" style="margin-top: 12px;">
                                    <strong>Moderator:</strong> {{ $project->moderation_note }}
                                </x-lab::alert>
                            @endif
                        </div>
                    </div>

                    <div style="margin-top: 16px; display: flex; gap: 8px; flex-wrap: wrap;">
                        @if ($project->status === 'approved')
                            <x-lab::button variant="secondary" size="sm" :href="$project->public_url" target="_blank">View live &nearr;</x-lab::button>
                        @endif
                        <x-lab::button variant="secondary" size="sm" :href="route('projects.edit', $project)">Edit</x-lab::button>
                        <form method="POST" action="{{ route('projects.destroy', $project) }}" style="display: inline;" onsubmit="return confirm('Delete this project?');">
                            @csrf
                            @method('DELETE')
                            <x-lab::button variant="danger" size="sm" type="submit">Delete</x-lab::button>
                        </form>
                    </div>
                </x-lab::card>
            @endforeach
        </div>
    @endif
</x-layouts.app>