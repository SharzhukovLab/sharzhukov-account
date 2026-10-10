<x-layouts.app title="Messages — Sharzhukov Account">
    <div class="page-header">
        <span class="lab-eyebrow">// Messages</span>
        <h1>My messages</h1>
        <p>Your inquiries and our replies — all in one place.</p>
    </div>

    @if ($messages->isEmpty())
        <x-lab::card style="text-align: center; padding: 60px 40px;">
            <p style="color: var(--lab-fg-muted); margin-bottom: 20px;">You haven't sent us anything yet.</p>
            <x-lab::button variant="primary" href="https://sharzhukov.com/contact">Send a message</x-lab::button>
        </x-lab::card>
    @else
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach ($messages as $message)
                <x-lab::card>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 15px; font-weight: 600; color: var(--lab-fg);">{{ $message->subject }}</div>
                            <div style="font-size: 12px; color: var(--lab-fg-subtle); margin-top: 2px;">{{ $message->created_at->format('M d, Y · H:i') }}</div>
                        </div>
                        @switch($message->status)
                            @case('new')      <x-lab::badge variant="accent">New</x-lab::badge> @break
                            @case('read')     <x-lab::badge>Read</x-lab::badge> @break
                            @case('replied')  <x-lab::badge variant="success">Replied</x-lab::badge> @break
                            @case('archived') <x-lab::badge>Archived</x-lab::badge> @break
                        @endswitch
                    </div>

                    <div style="padding: 12px 14px; background: var(--lab-bg-elevated); border-radius: 10px; margin-bottom: 12px;">
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--lab-fg-subtle); font-family: var(--font-mono); margin-bottom: 6px;">Your message</div>
                        <p style="margin: 0; font-size: 14px; color: var(--lab-fg);">{{ $message->message }}</p>
                    </div>

                    @if ($message->reply_body)
                        <div style="padding: 12px 14px; background: rgba(37, 99, 235, 0.05); border: 1px solid rgba(37, 99, 235, 0.15); border-radius: 10px;">
                            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-600); font-family: var(--font-mono); margin-bottom: 6px;">
                                Sharzhukov's Laboratory
                                @if ($message->replied_at)
                                    <span style="color: var(--lab-fg-subtle); margin-left: 8px;">{{ $message->replied_at->format('M d, Y · H:i') }}</span>
                                @endif
                            </div>
                            <p style="margin: 0; font-size: 14px; color: var(--lab-fg);">{!! nl2br(e($message->reply_body)) !!}</p>
                        </div>
                    @else
                        <div style="font-size: 13px; color: var(--lab-fg-muted); text-align: center; padding: 8px;">
                            Awaiting reply — we usually respond within 24–48 hours.
                        </div>
                    @endif
                </x-lab::card>
            @endforeach
        </div>
    @endif
</x-layouts.app>