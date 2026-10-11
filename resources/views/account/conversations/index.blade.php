<x-layouts.app title="Messages — Sharzhukov Account">
    <div class="page-header">
        <span class="lab-eyebrow">// Inbox</span>
        <h1>Messages</h1>
        <p>Private conversations with other members.</p>
    </div>

    @if($conversations->isEmpty())
        <x-lab::card style="text-align:center;padding:60px 40px;">
            <p style="color:var(--lab-fg-muted);margin:0 0 20px;">No conversations yet.</p>
            <p style="color:var(--lab-fg-muted);font-size:13px;margin:0;">
                Open anyone's <a href="https://sharzhukov.com/projects">profile</a> and tap <strong>Message</strong> to start.
            </p>
        </x-lab::card>
    @else
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach($conversations as $c)
                @php
                    $other = $c->users->firstWhere('id', '!=', $me->id);
                    $last  = $c->messages->first();
                    $unread = $c->unreadCountFor($me->id);
                @endphp
                <a href="{{ route('conversations.show', $c) }}" style="text-decoration:none;color:inherit;">
                    <div class="brutal-card" style="padding:16px;display:flex;gap:14px;align-items:center;transition:transform .12s,box-shadow .12s;">
                        <img src="{{ $other?->avatar_url }}" alt="{{ $other?->name }}"
                             style="width:48px;height:48px;flex-shrink:0;border:2px solid var(--brutal-border);object-fit:cover;">
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;justify-content:space-between;align-items:baseline;gap:10px;margin-bottom:4px;">
                                <div style="font-family:var(--font-sans);font-weight:900;font-size:15px;letter-spacing:-0.01em;">
                                    {{ $other?->name ?? 'Unknown' }}
                                </div>
                                @if($unread > 0)
                                    <span style="background:var(--brutal-accent);color:#fff;font-family:var(--font-mono);font-size:10px;font-weight:700;padding:3px 8px;">{{ $unread }} NEW</span>
                                @elseif($c->last_message_at)
                                    <span style="font-family:var(--font-mono);font-size:10px;color:var(--lab-fg-muted);">{{ $c->last_message_at->diffForHumans(null, true, true) }}</span>
                                @endif
                            </div>
                            <div style="font-family:var(--font-mono);font-size:11px;color:var(--lab-fg-muted);margin-bottom:4px;">
                                &#64;{{ $other?->username }}
                            </div>
                            @if($last)
                                <div style="font-size:13px;color:var(--lab-fg-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ \Illuminate\Support\Str::limit($last->body, 80) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-layouts.app>