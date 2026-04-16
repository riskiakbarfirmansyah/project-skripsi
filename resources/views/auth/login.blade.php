<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — {{ config('app.name', 'Radio Management') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:300,400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent: #7c6ce0;
            --accent-dark: #6455c4;
            --accent-soft: #a99df5;
            --accent-glow: rgba(124,108,224,0.22);
            --card-bg: rgba(255,255,255,0.6);
            --card-border: rgba(255,255,255,0.85);
            --input-bg: rgba(255,255,255,0.75);
            --input-border: rgba(180,170,240,0.38);
            --text-dark: #2d2a45;
            --text-mid:  #5c5880;
            --text-soft: #8e8ab5;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            overflow: hidden;
        }

        /* ── Aurora Background ── */
        .aurora-bg {
            position: fixed; inset: 0;
            background: linear-gradient(145deg, #d6eeff 0%, #e5ddfb 28%, #ede5ff 52%, #fde8d5 74%, #d4f0e8 100%);
            z-index: 0;
        }
        .aurora-bg::before, .aurora-bg::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            mix-blend-mode: multiply;
            opacity: 0.75;
            animation: drift 14s ease-in-out infinite alternate;
        }
        .aurora-bg::before {
            width: 65vw; height: 65vw;
            background: radial-gradient(circle, rgba(160,140,255,0.6) 0%, rgba(100,180,255,0.32) 50%, transparent 75%);
            top: -15%; right: -10%;
            animation-duration: 12s;
        }
        .aurora-bg::after {
            width: 55vw; height: 55vw;
            background: radial-gradient(circle, rgba(255,200,150,0.48) 0%, rgba(180,230,220,0.38) 50%, transparent 72%);
            bottom: -15%; left: -10%;
            animation-duration: 16s;
            animation-delay: -6s;
        }
        .orb-mid {
            position: fixed;
            width: 42vw; height: 42vw;
            background: radial-gradient(circle, rgba(120,200,255,0.42) 0%, rgba(200,160,255,0.28) 55%, transparent 75%);
            border-radius: 50%;
            filter: blur(75px);
            mix-blend-mode: multiply;
            top: 25%; left: 28%;
            opacity: 0.85;
            animation: drift 18s ease-in-out infinite alternate-reverse;
            z-index: 0;
        }
        @keyframes drift {
            0%   { transform: translate(0, 0) scale(1); }
            33%  { transform: translate(3%, -4%) scale(1.04); }
            66%  { transform: translate(-2%, 3%) scale(0.97); }
            100% { transform: translate(4%, 2%) scale(1.06); }
        }

        /* ── Layout ── */
        .auth-wrapper {
            position: relative; z-index: 2;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; padding: 2rem 1rem;
        }

        /* ── Card ── */
        .auth-card {
            width: 100%; max-width: 440px;
            background: var(--card-bg);
            border: 1.5px solid var(--card-border);
            border-radius: 32px;
            padding: 2.8rem 2.5rem 2.5rem;
            backdrop-filter: blur(40px) saturate(2);
            -webkit-backdrop-filter: blur(40px) saturate(2);
            box-shadow:
                0 2px 0 rgba(255,255,255,0.95) inset,
                0 1px 0 rgba(255,255,255,0.5) inset,
                0 32px 80px rgba(100,80,200,0.18),
                0 8px 24px rgba(80,100,200,0.12),
                0 2px 8px rgba(120,100,220,0.08);
            animation: rise 0.65s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        @keyframes rise {
            from { opacity: 0; transform: translateY(32px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .card-logo {
            display: flex; justify-content: center; align-items: center;
            margin-bottom: 1.3rem;
        }
        .card-logo img {
            height: 52px;
            filter: drop-shadow(0 4px 12px rgba(124,108,224,0.25));
            transition: filter 0.3s;
        }
        .card-logo img:hover {
            filter: drop-shadow(0 6px 18px rgba(124,108,224,0.4));
        }

        /* ── Decorative divider ── */
        .card-divider {
            position: relative;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(160,140,255,0.4) 30%, rgba(100,180,255,0.35) 70%, transparent 100%);
            margin-bottom: 1.8rem;
        }
        .card-divider::before {
            content: '';
            position: absolute;
            left: 50%; top: 50%;
            transform: translate(-50%, -50%);
            width: 6px; height: 6px;
            background: linear-gradient(135deg, var(--accent-soft), #7ab3f5);
            border-radius: 50%;
            box-shadow: 0 0 8px rgba(124,108,224,0.5);
        }

        /* ── Heading ── */
        .card-title {
            font-size: 1.2rem; font-weight: 700;
            color: var(--text-dark); margin-bottom: 0.35rem;
            text-align: center;
            letter-spacing: -0.01em;
        }
        .card-sub {
            font-size: 0.82rem; color: var(--text-soft);
            text-align: center; margin-bottom: 1.8rem;
        }

        /* ── Form ── */
        .fg { margin-bottom: 1.15rem; }
        .fl {
            display: block; font-size: 0.72rem; font-weight: 600;
            letter-spacing: 0.07em; text-transform: uppercase;
            color: var(--text-mid); margin-bottom: 0.45rem;
        }

        /* ── Input wrapper with icon ── */
        .input-wrap {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 0.9rem; top: 50%; transform: translateY(-50%);
            color: var(--text-soft);
            pointer-events: none;
            transition: color 0.25s;
            display: flex; align-items: center;
        }
        .input-icon svg { width: 16px; height: 16px; }
        .input-wrap:focus-within .input-icon { color: var(--accent); }

        .fi {
            width: 100%;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 14px;
            color: var(--text-dark);
            font-family: inherit; font-size: 0.93rem;
            padding: 0.72rem 1rem 0.72rem 2.55rem;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.2s;
            outline: none;
            backdrop-filter: blur(8px);
            box-shadow: 0 1px 0 rgba(255,255,255,0.85) inset, 0 2px 6px rgba(120,100,200,0.07);
        }
        .fi:focus {
            border-color: rgba(124,108,224,0.6);
            background: rgba(255,255,255,0.92);
            box-shadow:
                0 0 0 4px var(--accent-glow),
                0 1px 0 rgba(255,255,255,0.95) inset;
        }
        .fi.is-invalid {
            border-color: rgba(224,92,122,0.65);
        }
        .fi.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(224,92,122,0.12), 0 1px 0 rgba(255,255,255,0.95) inset;
        }
        .fi::placeholder { color: rgba(130,120,180,0.42); }

        /* ── Password toggle ── */
        .pw-toggle {
            position: absolute;
            right: 0.85rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; padding: 4px;
            color: var(--text-soft);
            display: flex; align-items: center;
            border-radius: 6px;
            transition: color 0.2s, background 0.2s;
        }
        .pw-toggle:hover {
            color: var(--accent);
            background: rgba(124,108,224,0.08);
        }
        .pw-toggle svg { width: 16px; height: 16px; }
        .fi-pw { padding-right: 2.6rem; }

        /* ── Remember row ── */
        .remember-row {
            display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 1.5rem;
        }
        .remember-label {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 0.82rem; color: var(--text-mid); cursor: pointer;
            user-select: none;
        }
        .remember-label input[type="checkbox"] {
            appearance: none; -webkit-appearance: none;
            width: 16px; height: 16px;
            border: 1.5px solid var(--input-border);
            border-radius: 5px;
            background: var(--input-bg);
            cursor: pointer;
            position: relative;
            transition: border-color 0.2s, background 0.2s;
            flex-shrink: 0;
        }
        .remember-label input[type="checkbox"]:checked {
            background: var(--accent);
            border-color: var(--accent);
        }
        .remember-label input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            left: 3px; top: 1px;
            width: 8px; height: 5px;
            border-left: 2px solid #fff;
            border-bottom: 2px solid #fff;
            transform: rotate(-45deg);
        }
        .link-sm {
            font-size: 0.82rem; color: var(--accent);
            text-decoration: none; font-weight: 500;
            transition: color 0.2s;
        }
        .link-sm:hover { color: var(--accent-dark); text-decoration: underline; }

        /* ── Submit ── */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #9b8af4 0%, #7c6ce0 50%, #6a9df7 100%);
            background-size: 200% 200%;
            background-position: 0% 50%;
            border: none; border-radius: 14px;
            color: #fff; font-family: inherit;
            font-size: 0.95rem; font-weight: 600;
            padding: 0.85rem 1.6rem; cursor: pointer;
            letter-spacing: 0.04em;
            box-shadow:
                0 4px 18px rgba(124,108,224,0.38),
                0 1px 0 rgba(255,255,255,0.3) inset;
            transition: transform 0.2s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.22s, background-position 0.4s;
            margin-bottom: 1.2rem;
            min-height: 48px;
            position: relative;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        }
        .btn-login:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow:
                0 10px 28px rgba(124,108,224,0.44),
                0 1px 0 rgba(255,255,255,0.3) inset;
            background-position: 100% 50%;
        }
        .btn-login:active:not(:disabled) {
            transform: translateY(0) scale(0.99);
            box-shadow: 0 4px 14px rgba(124,108,224,0.32), 0 1px 0 rgba(255,255,255,0.25) inset;
        }
        .btn-login:disabled {
            cursor: not-allowed;
            opacity: 0.85;
        }

        /* ── Spinner ── */
        .btn-spinner {
            display: none;
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.65s linear infinite;
            flex-shrink: 0;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .btn-login.loading .btn-spinner { display: block; }
        .btn-login.loading .btn-label { opacity: 0.85; }

        /* ── Footer links ── */
        .card-footer {
            text-align: center; font-size: 0.82rem;
            color: var(--text-soft); margin-top: 0.4rem;
        }
        .card-footer a { color: var(--accent); font-weight: 500; text-decoration: none; }
        .card-footer a:hover { color: var(--accent-dark); text-decoration: underline; }

        /* ── Error / Status messages ── */
        .err-txt {
            font-size: 0.78rem; color: #d94f6e;
            margin-top: 0.35rem;
            display: flex; align-items: center; gap: 0.3rem;
        }
        .err-txt::before {
            content: '';
            display: inline-block;
            width: 4px; height: 4px;
            background: #d94f6e;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .status-msg {
            background: rgba(58,171,140,0.1); border: 1px solid rgba(58,171,140,0.3);
            border-radius: 12px; padding: 0.65rem 1rem;
            font-size: 0.82rem; color: #2e9c78;
            margin-bottom: 1.2rem;
            display: flex; align-items: center; gap: 0.5rem;
        }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .auth-card {
                padding: 2.2rem 1.6rem 2rem;
                border-radius: 24px;
            }
            .btn-login { min-height: 52px; font-size: 1rem; }
        }
    </style>
</head>
<body>
    <div class="aurora-bg"></div>
    <div class="orb-mid"></div>

    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="card-logo">
                <img src="{{ asset('/image/LOGO RKB.png') }}" alt="Logo RKB">
            </div>
            <div class="card-divider"></div>

            <div class="card-title">Selamat Datang</div>
            <div class="card-sub">Masuk ke Sistem Manajemen Radio</div>

            @if (session('status'))
                <div class="status-msg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                <div class="fg">
                    <label class="fl" for="email">Email</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="3"/><path d="m2 7 10 7 10-7"/></svg>
                        </span>
                        <input id="email" type="email" name="email" class="fi {{ $errors->has('email') ? 'is-invalid' : '' }}"
                               value="{{ old('email') }}" required autofocus autocomplete="username"
                               placeholder="email@example.com">
                    </div>
                    @error('email')
                        <div class="err-txt">{{ $message }}</div>
                    @enderror
                </div>

                <div class="fg">
                    <label class="fl" for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input id="password" type="password" name="password" class="fi fi-pw {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               required autocomplete="current-password"
                               placeholder="••••••••">
                        <button type="button" class="pw-toggle" id="pw-toggle" aria-label="Tampilkan password">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="eye-off-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="err-txt">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Ingat saya
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="link-sm">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login" id="btn-login">
                    <span class="btn-spinner"></span>
                    <span class="btn-label">Masuk</span>
                </button>
            </form>

            <!-- @if (Route::has('register'))
                <div class="card-footer">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
                </div>
            @endif -->
        </div>
    </div>

    <script>
        // Password visibility toggle
        const pwToggle = document.getElementById('pw-toggle');
        const pwInput  = document.getElementById('password');
        const eyeIcon    = document.getElementById('eye-icon');
        const eyeOffIcon = document.getElementById('eye-off-icon');

        if (pwToggle) {
            pwToggle.addEventListener('click', function () {
                const isHidden = pwInput.type === 'password';
                pwInput.type = isHidden ? 'text' : 'password';
                eyeIcon.style.display    = isHidden ? 'none'  : '';
                eyeOffIcon.style.display = isHidden ? ''      : 'none';
                this.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
            });
        }

        // Loading state on submit
        const loginForm = document.getElementById('login-form');
        const btnLogin  = document.getElementById('btn-login');

        if (loginForm) {
            loginForm.addEventListener('submit', function () {
                btnLogin.classList.add('loading');
                btnLogin.disabled = true;
            });
        }
    </script>
</body>
</html>

