<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — {{ config('app.name', 'Radio Management') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('/image/LOGO.png') }}">
    <link rel="shortcut icon" href="{{ asset('/image/LOGO.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:300,400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent: #7c6ce0;
            --accent-soft: #a99df5;
            --card-bg: rgba(255,255,255,0.52);
            --card-border: rgba(255,255,255,0.78);
            --input-bg: rgba(255,255,255,0.72);
            --input-border: rgba(180,170,240,0.35);
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
            background: linear-gradient(145deg, #ddeeff 0%, #e8e2fa 30%, #f0e8ff 55%, #fce8d8 75%, #d8f0ec 100%);
            z-index: 0;
        }
        .aurora-bg::before, .aurora-bg::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            mix-blend-mode: multiply;
            opacity: 0.7;
            animation: drift 14s ease-in-out infinite alternate;
        }
        .aurora-bg::before {
            width: 65vw; height: 65vw;
            background: radial-gradient(circle, rgba(160,140,255,0.55) 0%, rgba(100,180,255,0.3) 50%, transparent 75%);
            top: -15%; right: -10%;
            animation-duration: 12s;
        }
        .aurora-bg::after {
            width: 55vw; height: 55vw;
            background: radial-gradient(circle, rgba(255,210,160,0.45) 0%, rgba(180,230,220,0.35) 50%, transparent 72%);
            bottom: -15%; left: -10%;
            animation-duration: 16s;
            animation-delay: -6s;
        }
        .orb-mid {
            position: fixed;
            width: 42vw; height: 42vw;
            background: radial-gradient(circle, rgba(120,200,255,0.4) 0%, rgba(200,160,255,0.25) 55%, transparent 75%);
            border-radius: 50%;
            filter: blur(75px);
            mix-blend-mode: multiply;
            top: 25%; left: 28%;
            opacity: 0.8;
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
            border-radius: 28px;
            padding: 2.8rem 2.4rem 2.4rem;
            backdrop-filter: blur(36px) saturate(1.8);
            -webkit-backdrop-filter: blur(36px) saturate(1.8);
            box-shadow:
                0 2px 0 rgba(255,255,255,0.9) inset,
                0 24px 60px rgba(120,100,210,0.14),
                0 6px 18px rgba(100,120,200,0.1);
            animation: rise 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        @keyframes rise {
            from { opacity: 0; transform: translateY(28px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .card-logo {
            display: flex; justify-content: center; align-items: center;
            margin-bottom: 1.2rem;
        }
        .card-logo img { height: 48px; }
        .card-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(160,140,255,0.3), rgba(100,180,255,0.25), transparent);
            margin-bottom: 1.8rem;
        }

        /* ── Heading ── */
        .card-title {
            font-size: 1.15rem; font-weight: 700;
            color: var(--text-dark); margin-bottom: 0.4rem;
            text-align: center;
        }
        .card-sub {
            font-size: 0.82rem; color: var(--text-soft);
            text-align: center; margin-bottom: 1.8rem;
        }

        /* ── Form ── */
        .fg { margin-bottom: 1.1rem; }
        .fl {
            display: block; font-size: 0.75rem; font-weight: 600;
            letter-spacing: 0.07em; text-transform: uppercase;
            color: var(--text-mid); margin-bottom: 0.45rem;
        }
        .fi {
            width: 100%;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 12px;
            color: var(--text-dark);
            font-family: inherit; font-size: 0.93rem;
            padding: 0.7rem 1rem;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.2s;
            outline: none;
            backdrop-filter: blur(8px);
            box-shadow: 0 1px 0 rgba(255,255,255,0.8) inset, 0 2px 6px rgba(120,100,200,0.07);
        }
        .fi:focus {
            border-color: rgba(140,110,240,0.55);
            background: rgba(255,255,255,0.88);
            box-shadow: 0 0 0 4px rgba(140,110,240,0.1), 0 1px 0 rgba(255,255,255,0.9) inset;
        }
        .fi.is-invalid { border-color: rgba(224,92,122,0.6); }
        .fi::placeholder { color: rgba(130,120,180,0.45); }

        /* ── Remember row ── */
        .remember-row {
            display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 1.4rem;
        }
        .remember-label {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 0.82rem; color: var(--text-mid); cursor: pointer;
        }
        .remember-label input[type="checkbox"] {
            accent-color: var(--accent);
            width: 14px; height: 14px;
        }
        .link-sm {
            font-size: 0.82rem; color: var(--accent);
            text-decoration: none; font-weight: 500;
            transition: color 0.2s;
        }
        .link-sm:hover { color: var(--accent-soft); }

        /* ── Submit ── */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #9b8af4 0%, #7ab3f5 100%);
            border: none; border-radius: 12px;
            color: #fff; font-family: inherit;
            font-size: 0.93rem; font-weight: 600;
            padding: 0.8rem 1.6rem; cursor: pointer;
            letter-spacing: 0.03em;
            box-shadow: 0 4px 16px rgba(140,110,240,0.3), 0 1px 0 rgba(255,255,255,0.25) inset;
            transition: transform 0.18s, box-shadow 0.2s;
            margin-bottom: 1.2rem;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(140,110,240,0.38), 0 1px 0 rgba(255,255,255,0.25) inset;
        }
        .btn-login:active { transform: translateY(0); }

        /* ── Footer links ── */
        .card-footer {
            text-align: center; font-size: 0.82rem;
            color: var(--text-soft); margin-top: 0.4rem;
        }
        .card-footer a { color: var(--accent); font-weight: 500; text-decoration: none; }
        .card-footer a:hover { color: var(--accent-soft); }

        /* ── Error / Status messages ── */
        .err-txt {
            font-size: 0.78rem; color: #e05c7a;
            margin-top: 0.3rem;
        }
        .status-msg {
            background: rgba(58,171,140,0.1); border: 1px solid rgba(58,171,140,0.3);
            border-radius: 10px; padding: 0.6rem 0.9rem;
            font-size: 0.82rem; color: #3aab8c;
            margin-bottom: 1.2rem;
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
                <div class="status-msg">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="fg">
                    <label class="fl" for="email">Email</label>
                    <input id="email" type="email" name="email" class="fi {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email') }}" required autofocus autocomplete="username"
                           placeholder="email@example.com">
                    @error('email')
                        <div class="err-txt">{{ $message }}</div>
                    @enderror
                </div>

                <div class="fg">
                    <label class="fl" for="password">Password</label>
                    <input id="password" type="password" name="password" class="fi {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           required autocomplete="current-password"
                           placeholder="••••••••">
                    @error('password')
                        <div class="err-txt">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Ingat saya
                    </label>
                    <!-- @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="link-sm">Lupa password?</a>
                    @endif -->
                </div>

                <button type="submit" class="btn-login">Masuk</button>
            </form>

            <!-- @if (Route::has('register'))
                <div class="card-footer">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
                </div>
            @endif -->
        </div>
    </div>
</body>
</html>

