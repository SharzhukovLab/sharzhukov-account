<x-layouts.app title="Sessions — Sharzhukov Account">
    <div class="page-header">
        <span class="lab-eyebrow">// Sessions</span>
        <h1>Active sessions</h1>
        <p>Devices currently signed in to your account.</p>
    </div>

    <x-lab::card>
        @if (empty($sessions))
            <p style="color: var(--lab-fg-muted); text-align: center; margin: 20px 0;">No active sessions.</p>
        @else
            @foreach ($sessions as $s)
                <div class="session-row">
                    <div style="min-width: 0; flex: 1;">
                        <div style="font-size: 14px; font-weight: 500; color: var(--lab-fg); margin-bottom: 2px;">
                            {{ \Illuminate\Support\Str::limit($s['ua'] ?? 'Unknown device', 80) }}
                            @if ($s['is_current'])
                                <x-lab::badge variant="success" style="margin-left: 8px;">This device</x-lab::badge>
                            @endif
                        </div>
                        <div class="session-meta">
                            IP {{ $s['ip'] }} &middot; {{ $s['last_active']->diffForHumans() }}
                        </div>
                    </div>
                    @if (! $s['is_current'])
                        <form method="POST" action="{{ route('sessions.destroy', $s['id']) }}">
                            @csrf
                            @method('DELETE')
                            <x-lab::button variant="secondary" size="sm" type="submit">Sign out</x-lab::button>
                        </form>
                    @endif
                </div>
            @endforeach
        @endif
    </x-lab::card>
</x-layouts.app>