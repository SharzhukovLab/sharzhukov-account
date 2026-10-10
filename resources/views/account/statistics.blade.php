<x-layouts.app title="Statistics — Sharzhukov Account">
    <div class="page-header">
        <span class="lab-eyebrow">// Statistics</span>
        <h1>Statistics</h1>
        <p>Your activity across Sharzhukov's Laboratory.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-tile"><div class="stat-value">{{ $stats['comments'] }}</div><div class="stat-label">Comments</div></div>
        <div class="stat-tile"><div class="stat-value">{{ $stats['comment_reactions'] }}</div><div class="stat-label">Reactions</div></div>
        <div class="stat-tile"><div class="stat-value">{{ $stats['projects_approved'] }}</div><div class="stat-label">Projects live</div></div>
        <div class="stat-tile"><div class="stat-value">{{ $stats['projects_pending'] }}</div><div class="stat-label">Pending review</div></div>
        <div class="stat-tile"><div class="stat-value">{{ number_format($stats['projects_views']) }}</div><div class="stat-label">Project views</div></div>
        <div class="stat-tile"><div class="stat-value">{{ $stats['wiki_revisions'] }}</div><div class="stat-label">Wiki edits</div></div>
        <div class="stat-tile"><div class="stat-value">{{ $stats['contact_messages'] }}</div><div class="stat-label">Messages sent</div></div>
        <div class="stat-tile"><div class="stat-value">{{ $stats['sessions'] }}</div><div class="stat-label">Active sessions</div></div>
    </div>

    <x-lab::card style="margin-top: 24px;">
        <h2 style="margin-top: 0;">Quick links</h2>
        <div class="card-row" style="margin-bottom: 12px;">
            <div><strong>My comments</strong><small>Moderated on sharzhukov.com</small></div>
            <x-lab::button variant="secondary" size="sm" href="https://sharzhukov.com/u/{{ $user->username }}" target="_blank">Public profile &nearr;</x-lab::button>
        </div>
        <div class="card-row">
            <div><strong>My projects</strong><small>Manage your submissions</small></div>
            <x-lab::button variant="secondary" size="sm" :href="route('projects.index')">Manage</x-lab::button>
        </div>
    </x-lab::card>
</x-layouts.app>