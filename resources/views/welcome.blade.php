<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Radio RKB') }} — Sistem Manajemen</title>
    <link rel="icon" type="image/png" href="{{ asset('/image/LOGO.png') }}">
    <link rel="shortcut icon" href="{{ asset('/image/LOGO.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent:      #7c6ce0;
            --accent-soft: #a99df5;
            --card-bg:     rgba(255,255,255,0.52);
            --card-border: rgba(255,255,255,0.78);
            --text-dark:   #2d2a45;
            --text-mid:    #5c5880;
            --text-soft:   #8e8ab5;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
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
            0%   { transform: translate(0, 0)   scale(1); }
            33%  { transform: translate(3%, -4%) scale(1.04); }
            66%  { transform: translate(-2%, 3%) scale(0.97); }
            100% { transform: translate(4%, 2%)  scale(1.06); }
        }

        /* ── Page Wrapper ── */
        .page-wrap {
            position: relative; z-index: 2;
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* ── Navbar ── */
        .navbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.1rem 2.5rem;
            background: rgba(255,255,255,0.55);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.7);
            position: sticky; top: 0; z-index: 10;
        }
        .nav-brand {
            display: flex; align-items: center; gap: 0.65rem;
            text-decoration: none;
        }
        .nav-brand img { height: 38px; }
        .nav-brand-text {
            font-size: 0.95rem; font-weight: 700;
            color: var(--text-dark);
        }
        .btn-nav {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.5rem 1.3rem; border-radius: 10px; border: none;
            font-family: inherit; font-size: 0.88rem; font-weight: 600;
            cursor: pointer; text-decoration: none;
            background: linear-gradient(135deg, #9b8af4 0%, #7ab3f5 100%);
            color: #fff;
            box-shadow: 0 3px 12px rgba(140,110,240,0.28);
            transition: transform 0.18s, box-shadow 0.2s;
        }
        .btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(140,110,240,0.36);
        }
        .btn-nav svg { width: 15px; height: 15px; }

        /* ── Hero ── */
        .hero {
            flex: 1;
            display: flex; align-items: center;
            padding: 4rem 2.5rem 3rem;
            max-width: 1100px; margin: 0 auto; width: 100%;
            gap: 3rem;
        }
        .hero-text { flex: 1; }

        .hero-badge {
            display: inline-flex; align-items: center; gap: 0.45rem;
            background: rgba(124,108,224,0.1);
            border: 1px solid rgba(124,108,224,0.22);
            border-radius: 20px; padding: 0.3rem 0.8rem;
            font-size: 0.75rem; font-weight: 600; color: var(--accent);
            margin-bottom: 1.2rem; letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .hero-badge .dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--accent);
            animation: pulse-dot 1.8s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.7); }
        }

        .hero-title {
            font-size: clamp(1.9rem, 4vw, 2.8rem);
            font-weight: 800; line-height: 1.2;
            color: var(--text-dark); margin-bottom: 0.9rem;
        }
        .hero-title em {
            font-style: normal;
            background: linear-gradient(135deg, #9b8af4 0%, #7ab3f5 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-sub {
            font-size: 1rem; color: var(--text-mid);
            line-height: 1.7; margin-bottom: 2rem; max-width: 460px;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Radio Visual ── */
        .hero-visual {
            flex: 0 0 auto;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 2rem;
        }

        /* Broadcast Icon with pulsing rings */
        .broadcast-icon {
            position: relative;
            display: flex; align-items: center; justify-content: center;
            width: 160px; height: 160px;
        }
        .ring {
            position: absolute;
            border-radius: 50%;
            border: 2px solid rgba(124,108,224,0.35);
            animation: ring-pulse 3s ease-out infinite;
        }
        .ring:nth-child(1) { width: 78px;  height: 78px;  animation-delay: 0s;   }
        .ring:nth-child(2) { width: 118px; height: 118px; animation-delay: 0.9s; }
        .ring:nth-child(3) { width: 158px; height: 158px; animation-delay: 1.8s; }
        @keyframes ring-pulse {
            0%   { transform: scale(0.7); opacity: 0.9; }
            100% { transform: scale(1.2); opacity: 0;   }
        }

        .mic-circle {
            position: relative; z-index: 2;
            width: 72px; height: 72px; border-radius: 50%;
            background: linear-gradient(135deg, rgba(155,138,244,0.9) 0%, rgba(122,179,245,0.85) 100%);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.65);
            box-shadow:
                0 8px 32px rgba(124,108,224,0.35),
                0 0 0 1px rgba(255,255,255,0.3) inset;
            display: flex; align-items: center; justify-content: center;
            animation: float 4s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-8px); }
        }
        .mic-circle svg { width: 32px; height: 32px; }

        /* Sound Wave Bars */
        .wave-bars {
            display: flex; align-items: flex-end;
            gap: 5px; height: 56px;
        }
        .bar {
            width: 7px; border-radius: 4px 4px 3px 3px;
            background: linear-gradient(180deg, #9b8af4 0%, #7ab3f5 100%);
            opacity: 0.85;
            animation: wave-bar var(--dur, 1s) ease-in-out var(--delay, 0s) infinite alternate;
        }
        @keyframes wave-bar {
            0%   { transform: scaleY(0.18); }
            100% { transform: scaleY(1);    }
        }

        /* ── Footer ── */
        footer {
            text-align: center;
            padding: 1.2rem;
            font-size: 0.78rem; color: var(--text-soft);
            background: rgba(255,255,255,0.3);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255,255,255,0.5);
        }

        /* ── Secondary CTA (Daftar Narasumber) ── */
        .hero-cta-secondary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.78rem 1.8rem; border-radius: 12px;
            font-family: inherit; font-size: 0.95rem; font-weight: 700;
            cursor: pointer; text-decoration: none; border: 2px solid rgba(124,108,224,0.45);
            background: rgba(255,255,255,0.55);
            color: var(--accent);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 16px rgba(124,108,224,0.12);
            transition: transform 0.18s, box-shadow 0.2s, background 0.2s;
            margin-top: 0.75rem;
            animation: rise 0.9s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        .hero-cta-secondary:hover {
            transform: translateY(-3px);
            background: rgba(255,255,255,0.78);
            box-shadow: 0 8px 24px rgba(124,108,224,0.22);
        }
        .hero-cta-secondary svg { width: 16px; height: 16px; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .hero { flex-direction: column-reverse; text-align: center; padding: 2.5rem 1.5rem 2rem; gap: 2rem; }
            .hero-text { display: flex; flex-direction: column; align-items: center; }
            .hero-sub { max-width: 100%; }
            .navbar { padding: 0.9rem 1.2rem; }
        }
    </style>
</head>
<body>

<div class="aurora-bg"></div>
<div class="orb-mid"></div>

<div class="page-wrap">

    {{-- ── Navbar ── --}}
    <nav class="navbar">
        <a href="/" class="nav-brand">
            <img src="{{ asset('/image/LOGO RKB.png') }}" alt="Logo RKB">
            <span class="nav-brand-text">{{ config('app.name', 'Radio RKB') }}</span>
        </a>
        @auth
            <a href="{{ route('dashboard') }}" class="btn-nav">
                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="1" y="1" width="6" height="6" rx="1" stroke="white" stroke-width="1.4"/>
                    <rect x="9" y="1" width="6" height="6" rx="1" stroke="white" stroke-width="1.4"/>
                    <rect x="1" y="9" width="6" height="6" rx="1" stroke="white" stroke-width="1.4"/>
                    <rect x="9" y="9" width="6" height="6" rx="1" stroke="white" stroke-width="1.4"/>
                </svg>
                Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="btn-nav">
                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 2H12.5C13.3 2 14 2.7 14 3.5v9c0 .8-.7 1.5-1.5 1.5H10" stroke="white" stroke-width="1.4" stroke-linecap="round"/>
                    <path d="M6.5 11l3-3L6.5 5" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="9.5" y1="8" x2="2" y2="8" stroke="white" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
                Masuk
            </a>
        @endauth
    </nav>

    {{-- ── Hero ── --}}
    <section class="hero">
        <div class="hero-text">
            <div class="hero-badge">
                <span class="dot"></span>
                Aplikasi Manajemen Radio
            </div>
            <h1 class="hero-title">
                Selamat datang di<br>
                <em>Aplikasi Manajemen Radio</em>
            </h1>
            <p class="hero-sub" style="margin-bottom:0.5rem;">
                Kelola operasional siaran lebih cepat dan terdokumentasi.
            </p>
            <p class="hero-sub">
                Ingin menjadi narasumber di program kami? Daftarkan data Anda melalui tombol
                <strong>"Daftar Sebagai Narasumber"</strong>. Tim kami akan meninjau pengajuan Anda
                dan menghubungi Anda untuk kebutuhan <strong>byphone</strong> atau <strong>talkshow</strong>.
            </p>
            <a href="{{ route('narasumber.register-page') }}" class="hero-cta-secondary">
                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1v14M1 8h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
                Daftar Sebagai Narasumber
            </a>
        </div>

        {{-- Radio visual: microphone + pulsing rings + sound wave bars --}}
        <div class="hero-visual">
            <div class="broadcast-icon">
                <div class="ring"></div>
                <div class="ring"></div>
                <div class="ring"></div>
                <div class="mic-circle">
                    {{-- Microphone icon --}}
                    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="11" y="3" width="10" height="16" rx="5" fill="white" fill-opacity="0.92"/>
                        <path d="M6 15c0 5.52 4.48 10 10 10s10-4.48 10-10" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                        <line x1="16" y1="25" x2="16" y2="29" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                        <line x1="11" y1="29" x2="21" y2="29" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            {{-- Animated equalizer bars --}}
            <div class="wave-bars">
                <div class="bar" style="height:18px; --dur:0.9s;  --delay:0.00s;"></div>
                <div class="bar" style="height:38px; --dur:1.1s;  --delay:0.10s;"></div>
                <div class="bar" style="height:26px; --dur:0.80s; --delay:0.20s;"></div>
                <div class="bar" style="height:48px; --dur:1.3s;  --delay:0.05s;"></div>
                <div class="bar" style="height:22px; --dur:0.75s; --delay:0.15s;"></div>
                <div class="bar" style="height:42px; --dur:1.0s;  --delay:0.30s;"></div>
                <div class="bar" style="height:16px; --dur:0.85s; --delay:0.08s;"></div>
                <div class="bar" style="height:36px; --dur:1.15s; --delay:0.22s;"></div>
                <div class="bar" style="height:48px; --dur:0.95s; --delay:0.12s;"></div>
                <div class="bar" style="height:28px; --dur:1.05s; --delay:0.18s;"></div>
                <div class="bar" style="height:40px; --dur:0.70s; --delay:0.00s;"></div>
                <div class="bar" style="height:20px; --dur:1.2s;  --delay:0.25s;"></div>
            </div>
        </div>
    </section>

    {{-- ── Footer ── --}}
    <footer>
        &copy; {{ date('Y') }} {{ config('app.name', 'Radio RKB') }} — Aplikasi Manajemen Radio
    </footer>

</div>

</body>
</html>
