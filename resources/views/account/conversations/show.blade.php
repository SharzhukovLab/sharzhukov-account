<x-layouts.app :title="'Chat with ' . ($other?->name ?? 'Unknown') . ' — Sharzhukov Account'">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <a href="{{ route('conversations.index') }}" class="lab-btn lab-btn--secondary" style="font-size:11px;padding:8px 12px;">← INBOX</a>
            @if($other)
                <a href="https://sharzhukov.com/u/{{ $other->username }}" target="_blank" style="display:flex;align-items:center;gap:12px;text-decoration:none;color:inherit;">
                    <img src="{{ $other->avatar_url }}" alt="{{ $other->name }}" style="width:44px;height:44px;border:2px solid var(--brutal-border);object-fit:cover;">
                    <div>
                        <div style="font-family:var(--font-sans);font-weight:900;font-size:16px;">{{ $other->name }}</div>
                        <div style="font-family:var(--font-mono);font-size:11px;color:var(--lab-fg-muted);">&#64;{{ $other->username }}</div>
                    </div>
                </a>
            @else
                <span style="font-family:var(--font-mono);color:var(--lab-fg-muted);">@ deleted user</span>
            @endif
        </div>
    </div>

    @if(session('status') === 'message-sent')
        <x-lab::alert variant="success" style="margin-bottom:16px;">Message sent.</x-lab::alert>
    @endif

    <div class="brutal-card" style="padding:20px;max-height:60vh;overflow-y:auto;display:flex;flex-direction:column;gap:14px;margin-bottom:20px;" id="thread-scroll">
        @forelse($conversation->messages as $m)
            @php $isMe = $m->user_id === $me->id; @endphp
            <div style="display:flex;{{ $isMe ? 'justify-content:flex-end;' : '' }}">
                <div style="max-width:75%;padding:10px 14px;border:2px solid var(--brutal-border);{{ $isMe ? 'background:var(--brutal-accent);color:#fff;' : 'background:var(--lab-bg-elevated);' }}">
                    <div style="font-family:var(--font-mono);font-size:10px;font-weight:700;letter-spacing:0.1em;opacity:.7;margin-bottom:6px;text-transform:uppercase;">
                        {{ $isMe ? 'You' : $m->user->name }} · {{ $m->created_at->format('M d · H:i') }}
                    </div>
                    <div style="font-size:14px;line-height:1.5;white-space:pre-wrap;">{{ $m->body }}</div>
                </div>
            </div>
        @empty
            <p style="text-align:center;color:var(--lab-fg-muted);font-size:14px;margin:20px 0;">No messages yet — say hi!</p>
        @endforelse
    </div>

    <form method="POST" action="{{ route('conversations.store', $conversation) }}">
        @csrf
        @if($errors->any())
            <x-lab::alert variant="danger" style="margin-bottom:12px;">
                <ul style="margin:0;padding-left:20px;">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </x-lab::alert>
        @endif
        <div style="display:flex;gap:10px;align-items:flex-start;">
            <textarea name="body" rows="2" required maxlength="5000"
                      placeholder="Type your message…"
                      class="lab-input"
                      style="flex:1;resize:vertical;min-height:60px;font-family:var(--font-sans);font-size:14px;">{{ old('body') }}</textarea>
            <x-lab::button variant="primary" type="submit" style="align-self:stretch;padding:0 20px;">Send →</x-lab::button>
        </div>
    </form>

    <script>
        (function () {
            var el = document.getElementById('thread-scroll');
            if (el) el.scrollTop = el.scrollHeight;
        })();
    </script>
</x-layouts.app>