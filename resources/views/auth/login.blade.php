<x-layouts.guest title="Sign in — Sharzhukov Account">
    <div style="margin-bottom: 20px;">
        <span class="lab-eyebrow">// Sign in</span>
        <h1 style="margin: 6px 0 4px;">Welcome back</h1>
        <p style="color: var(--lab-fg-muted); font-size: 14px; margin: 0;">Sign in to your Sharzhukov's Laboratory account.</p>
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

    @if (session('status'))
        <x-lab::alert variant="success" style="margin-bottom: 16px;">{{ session('status') }}</x-lab::alert>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="hidden" name="return" value="{{ request('return') }}">

        <div class="field">
            <label for="email" class="lab-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="lab-input">
        </div>

        <div class="field">
            <label for="password" class="lab-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="lab-input">
        </div>

        <label class="checkbox-row">
            <input type="checkbox" name="remember">
            <span>Remember me</span>
        </label>

        <x-lab::button variant="primary" type="submit" style="width: 100%;">Sign in</x-lab::button>
    </form>

    <div style="margin-top: 20px; display: flex; justify-content: space-between; font-size: 13px;">
        <a href="{{ route('password.request') }}">Forgot password?</a>
        <a href="{{ route('register', ['return' => request('return')]) }}">Create account</a>
    </div>
    <div style="margin-top: 24px; padding-top: 20px; border-top: 2px dashed var(--brutal-border);">
        <button type="button" id="passkey-login-btn" class="lab-btn lab-btn--secondary" style="width: 100%; display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 6px;"><path d="M12 2a5 5 0 0 0-5 5v3"/><path d="M7 10a5 5 0 0 0 5 5h0"/><circle cx="12" cy="17" r="3"/><path d="M17 7a5 5 0 0 0-5-5"/></svg>
            Sign in with passkey
        </button>
        <p id="passkey-login-error" style="display:none; color: #dc2626; font-size: 13px; margin: 12px 0 0;"></p>
    </div>

    <script>
    (function () {
        var btn = document.getElementById('passkey-login-btn');
        var err = document.getElementById('passkey-login-error');
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!btn || !window.PublicKeyCredential) return;

        // Показываем кнопку только если браузер поддерживает passkeys
        if (typeof PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable === 'function') {
            btn.style.display = 'inline-flex';
        } else {
            btn.style.display = 'inline-flex';
        }

        function b64uToBuf(s) {
            s = s.replace(/-/g, '+').replace(/_/g, '/');
            while (s.length % 4) s += '=';
            var bin = atob(s), len = bin.length, buf = new Uint8Array(len);
            for (var i = 0; i < len; i++) buf[i] = bin.charCodeAt(i);
            return buf.buffer;
        }
        function bufToB64u(buf) {
            var bin = '', bytes = new Uint8Array(buf);
            for (var i = 0; i < bytes.byteLength; i++) bin += String.fromCharCode(bytes[i]);
            return btoa(bin).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
        }
        function prepGet(opts) {
            opts.challenge = b64uToBuf(opts.challenge);
            (opts.allowCredentials || []).forEach(function (c) { c.id = b64uToBuf(c.id); });
            return opts;
        }
        function serializeCred(cred) {
            if (typeof cred.toJSON === 'function') return cred.toJSON();
            var r = cred.response;
            return {
                id: cred.id,
                rawId: bufToB64u(cred.rawId),
                type: cred.type,
                response: {
                    clientDataJSON: bufToB64u(r.clientDataJSON),
                    authenticatorData: bufToB64u(r.authenticatorData),
                    signature: bufToB64u(r.signature),
                    userHandle: r.userHandle ? bufToB64u(r.userHandle) : null,
                },
                clientExtensionResults: typeof cred.getClientExtensionResults === 'function' ? cred.getClientExtensionResults() : {},
            };
        }
        function showErr(msg) {
            if (err) { err.textContent = msg; err.style.display = 'block'; }
        }

        btn.addEventListener('click', async function () {
            btn.disabled = true;
            var original = btn.innerHTML;
            btn.innerHTML = 'Waiting for device...';
            if (err) err.style.display = 'none';
            try {
                var optRes = await fetch('/passkeys/login/options', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfMeta.content },
                    credentials: 'same-origin',
                });
                if (!optRes.ok) throw new Error('Cannot start passkey login (' + optRes.status + ')');
                var options = await optRes.json();
                var publicKey = prepGet(options.publicKey || options);

                var cred = await navigator.credentials.get({ publicKey: publicKey });
                if (!cred) throw new Error('No credential returned');

                var returnUrl = new URLSearchParams(location.search).get('return') || '/dashboard';
                var payload = { credential: serializeCred(cred), return: returnUrl };

                var res = await fetch('/passkeys/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfMeta.content,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload),
                });

                if (res.redirected) {
                    window.location.href = res.url;
                    return;
                }
                if (!res.ok) {
                    var text = await res.text();
                    throw new Error('Login failed: ' + res.status + (text ? ' — ' + text.substring(0, 160) : ''));
                }
                var data = await res.json().catch(function () { return {}; });
                window.location.href = data.redirect || returnUrl;
            } catch (e) {
                showErr(e.message || 'Passkey login failed');
                btn.disabled = false;
                btn.innerHTML = original;
            }
        });
    })();
    </script>

</x-layouts.guest>