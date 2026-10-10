<x-layouts.app title="Overview — Sharzhukov Account">
    <div>
        <span class="brutal-marker">// OVERVIEW</span>
        <h1 class="brutal-hero-title" style="margin:24px 0 8px;font-size:clamp(2rem,4vw,3rem);">
            Welcome back, {{ $user->name }}
        </h1>
        <p style="color:var(--lab-fg-muted);font-size:16px;margin:0 0 40px;line-height:1.6;">
            Here's a quick overview of your account and activity.
        </p>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px;">

            <x-lab::card>
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;">
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <x-lucide-monitor style="width:20px;height:20px;" />
                            <span style="font-weight:800;font-size:15px;letter-spacing:-0.01em;">Active sessions</span>
                        </div>
                        <div style="font-family:var(--font-mono);font-size:12px;color:var(--lab-fg-muted);">
                            {{ $sessionsCount }} {{ $sessionsCount === 1 ? 'device' : 'devices' }}
                        </div>
                    </div>
                    <x-lab::button variant="secondary" size="sm" :href="route('sessions')">Manage</x-lab::button>
                </div>
            </x-lab::card>

            <x-lab::card>
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;">
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <x-lucide-shield-check style="width:20px;height:20px;" />
                            <span style="font-weight:800;font-size:15px;letter-spacing:-0.01em;">Two-factor auth</span>
                        </div>
                        <div>
                            @if ($user->two_factor_secret)
                                <x-lab::badge variant="success">Enabled</x-lab::badge>
                            @else
                                <x-lab::badge variant="danger">Disabled</x-lab::badge>
                            @endif
                        </div>
                    </div>
                    <x-lab::button variant="secondary" size="sm" :href="route('security')">Configure</x-lab::button>
                </div>
            </x-lab::card>

            <x-lab::card>
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;">
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <x-lucide-mail style="width:20px;height:20px;" />
                            <span style="font-weight:800;font-size:15px;letter-spacing:-0.01em;">My messages</span>
                        </div>
                        <div style="font-family:var(--font-mono);font-size:12px;color:var(--lab-fg-muted);">
                            Support inquiries and replies
                        </div>
                    </div>
                    <x-lab::button variant="secondary" size="sm" :href="route('messages')">Open</x-lab::button>
                </div>
            </x-lab::card>

            <x-lab::card>
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;">
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <x-lucide-rocket style="width:20px;height:20px;" />
                            <span style="font-weight:800;font-size:15px;letter-spacing:-0.01em;">My projects</span>
                        </div>
                        <div style="font-family:var(--font-mono);font-size:12px;color:var(--lab-fg-muted);">
                            Submit and manage your open-source projects
                        </div>
                    </div>
                    <x-lab::button variant="secondary" size="sm" :href="route('projects.index')">Open</x-lab::button>
                </div>
            </x-lab::card>

            <x-lab::card>
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;">
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <x-lucide-bar-chart-3 style="width:20px;height:20px;" />
                            <span style="font-weight:800;font-size:15px;letter-spacing:-0.01em;">Statistics</span>
                        </div>
                        <div style="font-family:var(--font-mono);font-size:12px;color:var(--lab-fg-muted);">
                            Activity across the ecosystem
                        </div>
                    </div>
                    <x-lab::button variant="secondary" size="sm" :href="route('statistics')">View</x-lab::button>
                </div>
            </x-lab::card>

            <x-lab::card>
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;">
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <x-lucide-at-sign style="width:20px;height:20px;" />
                            <span style="font-weight:800;font-size:15px;letter-spacing:-0.01em;">Email</span>
                        </div>
                        <div style="font-family:var(--font-mono);font-size:12px;color:var(--lab-fg-muted);word-break:break-all;margin-bottom:6px;">
                            {{ $user->email }}
                        </div>
                        @if ($user->email_verified_at)
                            <x-lab::badge variant="success">Verified</x-lab::badge>
                        @else
                            <x-lab::badge variant="danger">Not verified</x-lab::badge>
                        @endif
                    </div>
                    <x-lab::button variant="secondary" size="sm" :href="route('profile')">Profile</x-lab::button>
                </div>
            </x-lab::card>

        </div>
    </div>
</x-layouts.app>