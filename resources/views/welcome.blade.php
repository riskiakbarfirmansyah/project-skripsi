<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Radio RKB') }} — Aplikasi Manajemen</title>
    <link rel="icon" type="image/png" href="{{ asset('/image/LOGO.png') }}">
    <link rel="shortcut icon" href="{{ asset('/image/LOGO.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-color: #fafafa;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --accent-1: #7c3aed; /* Logo primary purple */
            --accent-2: #4f46e5; /* Indigo */
            --card-bg: rgba(255, 255, 255, 0.7);
            --card-border: rgba(124, 58, 237, 0.15);
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-muted);
            line-height: 1.6;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Modern Light Mesh Gradient Background ── */
        .bg-glow {
            position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none;
            background: #f8fafc;
        }
        .bg-glow::before, .bg-glow::after {
            content: ''; position: absolute; border-radius: 50%; filter: blur(100px);
            opacity: 0.15; animation: pulseGlow 10s ease-in-out infinite alternate;
        }
        .bg-glow::before {
            width: 60vw; height: 60vw; background: radial-gradient(circle, var(--accent-1), transparent 60%);
            top: -20%; left: -10%;
        }
        .bg-glow::after {
            width: 50vw; height: 50vw; background: radial-gradient(circle, var(--accent-2), transparent 60%);
            bottom: -20%; right: -10%; animation-delay: -5s;
        }
        @keyframes pulseGlow {
            0% { transform: scale(1) translate(0, 0); opacity: 0.12; }
            100% { transform: scale(1.1) translate(2%, 2%); opacity: 0.2; }
        }

        .page-wrap { position: relative; z-index: 2; display: flex; flex-direction: column; min-height: 100vh; }

        /* ── Glass Navbar ── */
        .navbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.25rem 5%;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            position: sticky; top: 0; z-index: 10;
        }
        .nav-brand { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
        .nav-brand img { height: 36px; }
        .nav-brand-text { font-size: 1.1rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.01em; }
        
        .btn-nav {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.6rem 1.25rem; border-radius: 99px;
            font-size: 0.875rem; font-weight: 600; text-decoration: none;
            background: #fff; color: var(--accent-1);
            border: 1px solid var(--card-border);
            box-shadow: 0 2px 4px rgba(124, 58, 237, 0.05);
            transition: all 0.3s ease;
        }
        .btn-nav:hover {
            background: #f5f3ff;
            border-color: rgba(124, 58, 237, 0.3);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.1);
        }
        .btn-nav svg { width: 16px; height: 16px; stroke: currentColor; }

        /* ── Hero Section ── */
        .hero {
            flex: 1; display: flex; align-items: center; justify-content: space-between;
            padding: 6rem 5% 4rem; max-width: 1280px; margin: 0 auto; width: 100%; gap: 4rem;
        }
        .hero-text { flex: 1; max-width: 600px; }

        .hero-badge {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.4rem 1rem; border-radius: 99px;
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
            background: rgba(124, 58, 237, 0.1); color: var(--accent-1);
            border: 1px solid rgba(124, 58, 237, 0.2);
            margin-bottom: 1.5rem;
        }
        .hero-badge .dot {
            width: 6px; height: 6px; border-radius: 50%; background: var(--accent-1);
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(0.8); } }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 800; line-height: 1.1;
            color: var(--text-main); margin-bottom: 1.25rem; letter-spacing: -0.03em;
        }
        .hero-title em {
            font-style: normal;
            background: linear-gradient(135deg, var(--accent-1) 0%, var(--accent-2) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        .hero-sub-main {
            font-size: 1.25rem; color: var(--text-muted); margin-bottom: 1.5rem; line-height: 1.6;
        }
        
        .hero-info-box {
            display: flex; align-items: flex-start; gap: 1rem;
            padding: 1.25rem; border-radius: 16px;
            background: #fff; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            margin-bottom: 2rem; max-width: 540px;
        }
        .hero-info-icon {
            width: 32px; height: 32px; border-radius: 8px; background: rgba(124, 58, 237, 0.1);
            display: flex; align-items: center; justify-content: center; color: var(--accent-1); flex-shrink: 0;
        }
        .hero-info-icon svg { width: 18px; height: 18px; }
        .hero-info-text { font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; }
        .hero-info-text strong { color: var(--text-main); font-weight: 700; }

        /* ── Premium CTA Button ── */
        .hero-cta-secondary {
            display: inline-flex; align-items: center; gap: 0.6rem;
            padding: 0.875rem 1.75rem; border-radius: 99px;
            font-size: 1rem; font-weight: 600; cursor: pointer;
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            color: #fff;
            border: none;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3), inset 0 1px 0 rgba(255,255,255,0.2);
            transition: all 0.3s ease; position: relative; overflow: hidden;
        }
        .hero-cta-secondary:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(124, 58, 237, 0.4); filter: brightness(1.05); }
        .hero-cta-secondary svg { width: 18px; height: 18px; }

        /* ── Modern Abstract Visual ── */
        .hero-visual { flex: 1; display: flex; align-items: center; justify-content: center; position: relative; }
        
        .visual-glass {
            position: relative; width: 320px; height: 320px;
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(255, 255, 255, 1);
            border-radius: 32px; backdrop-filter: blur(20px);
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2rem;
            box-shadow: 0 20px 40px rgba(124, 58, 237, 0.08);
            animation: floatCard 6s ease-in-out infinite;
        }
        @keyframes floatCard {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(1deg); }
        }

        .live-badge {
            position: absolute; top: -15px; right: -15px;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 99px;
            padding: 0.4rem 0.8rem; font-size: 0.75rem; font-weight: 800; color: #ef4444;
            display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            animation: floatBadge 3s ease-in-out infinite; z-index: 10;
        }
        .live-badge .live-dot { width: 6px; height: 6px; border-radius: 50%; background: #ef4444; animation: pulse-dot 1.5s infinite; }
        @keyframes floatBadge { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
        
        .floating-orb {
            position: absolute; border-radius: 50%; background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            filter: blur(12px); opacity: 0.4; z-index: -1;
        }
        .orb-1 { width: 80px; height: 80px; top: -20px; left: -30px; animation: floatOrb 5s ease-in-out infinite alternate; }
        .orb-2 { width: 60px; height: 60px; bottom: 30px; right: -20px; animation: floatOrb 4s ease-in-out infinite alternate-reverse; }
        @keyframes floatOrb { 0% { transform: translateY(0) scale(1); } 100% { transform: translateY(20px) scale(1.1); } }

        .mic-icon-wrapper {
            width: 88px; height: 88px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 10px 25px rgba(124, 58, 237, 0.3);
            position: relative;
        }
        .mic-icon-wrapper::after {
            content: ''; position: absolute; inset: -4px; border-radius: 50%;
            border: 1px solid rgba(124, 58, 237, 0.3);
            @if(!empty($isLive))
            animation: ripple 2s infinite cubic-bezier(0.4, 0, 0.2, 1);
            @endif
        }
        @keyframes ripple {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.5); opacity: 0; }
        }
        .mic-icon-wrapper svg { width: 40px; height: 40px; color: #fff; }

        .sound-waves { display: flex; align-items: center; gap: 6px; height: 40px; }
        .sound-wave {
            width: 6px; border-radius: 99px; background: var(--accent-1); opacity: 0.6;
            @if(!empty($isLive))
            animation: bounceWave 1s infinite alternate ease-in-out;
            @endif
        }
        .sound-wave:nth-child(1) { height: 16px; animation-delay: 0.1s; }
        .sound-wave:nth-child(2) { height: 32px; animation-delay: 0.2s; }
        .sound-wave:nth-child(3) { height: 24px; animation-delay: 0.3s; }
        .sound-wave:nth-child(4) { height: 40px; animation-delay: 0.4s; }
        .sound-wave:nth-child(5) { height: 20px; animation-delay: 0.5s; }
        .sound-wave:nth-child(6) { height: 36px; animation-delay: 0.6s; }
        .sound-wave:nth-child(7) { height: 16px; animation-delay: 0.7s; }
        @keyframes bounceWave { 0% { transform: scaleY(0.5); opacity: 0.5; } 100% { transform: scaleY(1); opacity: 1; } }

        /* ── Footer ── */
        footer {
            text-align: center; padding: 1.5rem; font-size: 0.875rem; color: #94a3b8;
            border-top: 1px solid rgba(0,0,0,0.05); background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(10px);
        }

        /* ── Sleek Modal Overlay ── */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 100;
            background: rgba(15, 23, 42, 0.3); backdrop-filter: blur(8px);
            display: flex; align-items: center; justify-content: center;
            padding: 1rem; opacity: 0; pointer-events: none; transition: all 0.3s ease;
        }
        .modal-overlay.active { opacity: 1; pointer-events: all; }
        
        .modal-box {
            background: #fff; border: 1px solid rgba(0,0,0,0.05);
            border-radius: 24px; width: 100%; max-width: 540px; max-height: 90vh; overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(124, 58, 237, 0.15);
            transform: translateY(20px) scale(0.95); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex; flex-direction: column; padding: 2rem; color: var(--text-main);
        }
        .modal-overlay.active .modal-box { transform: translateY(0) scale(1); }
        
        .modal-header { margin-bottom: 1.5rem; }
        .modal-title { font-size: 1.5rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em; }
        .modal-subtitle { font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem; }

        /* Tabs */
        .modal-tabs {
            display: flex; gap: 0.5rem; background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 12px; padding: 0.375rem; margin-bottom: 1.5rem;
        }
        .modal-tab {
            flex: 1; padding: 0.625rem; border: none; border-radius: 8px;
            font-size: 0.875rem; font-weight: 600; cursor: pointer;
            background: transparent; color: var(--text-muted); transition: all 0.2s;
        }
        .modal-tab:hover { color: var(--text-main); }
        .modal-tab.active { background: #fff; color: var(--accent-1); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .tab-desc-box {
            margin-bottom: 1.5rem; font-size: 0.825rem; line-height: 1.5;
            padding: 0.875rem 1.125rem; border-radius: 12px; background: rgba(124, 58, 237, 0.05);
            border: 1px solid rgba(124, 58, 237, 0.1); color: var(--text-muted);
            transition: all 0.3s ease;
        }
        .tab-desc-box strong { color: var(--accent-1); font-weight: 700; }

        /* Form */
        .form-grid { display: grid; grid-template-columns: 1fr; gap: 1.25rem; }
        .form-field { display: flex; flex-direction: column; gap: 0.5rem; }
        .form-label { font-size: 0.875rem; font-weight: 600; color: var(--text-main); }
        .form-input, .form-textarea, .date-input-wrap {
            padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 10px;
            font-family: inherit; font-size: 0.875rem; color: var(--text-main); background: #fff;
            transition: all 0.2s; outline: none; width: 100%;
        }
        .form-input::placeholder, .form-textarea::placeholder { color: #94a3b8; }
        .form-input:focus, .form-textarea:focus { border-color: var(--accent-1); box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1); }
        .form-textarea { resize: vertical; min-height: 90px; }
        .form-error { font-size: 0.75rem; color: #ef4444; }

        .date-input-wrap { position: relative; display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .date-input-wrap:hover { border-color: #94a3b8; }
        .date-input-wrap.has-value { border-color: var(--accent-1); }
        .date-input-wrap input[type="datetime-local"] { position: absolute; opacity: 0; width: 1px; height: 1px; pointer-events: none; }
        .date-disp { font-size: 0.875rem; color: var(--text-main); flex: 1; }
        .date-disp.ph { color: #94a3b8; }

        .form-btns { display: flex; gap: 1rem; margin-top: 2rem; }
        .btn-cancel {
            flex: 1; padding: 0.875rem; border-radius: 10px; border: 1px solid #e2e8f0;
            font-size: 0.875rem; font-weight: 600; cursor: pointer; background: #f8fafc; color: var(--text-muted);
            transition: all 0.2s;
        }
        .btn-cancel:hover { background: #e2e8f0; color: var(--text-main); }
        .btn-submit {
            flex: 1; padding: 0.875rem; border-radius: 10px; border: none;
            font-size: 0.875rem; font-weight: 600; cursor: pointer;
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2)); color: #fff;
            transition: all 0.2s; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.25);
        }
        .btn-submit:hover:not(:disabled) { box-shadow: 0 6px 20px rgba(124, 58, 237, 0.35); transform: translateY(-1px); filter: brightness(1.05); }
        .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

        /* Captcha */
        .captcha-row {
            margin-top: 1.5rem; padding: 1rem 1.25rem; background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 12px; display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        }
        .captcha-label-wrap { flex: 1; }
        .captcha-input-wrap { display: flex; align-items: center; gap: 10px; }
        .captcha-box {
            background: #fff; border: 1px solid #cbd5e1; border-radius: 8px;
            padding: 0.5rem 0.75rem; font-size: 0.875rem; font-weight: 700; color: var(--accent-1);
            display: flex; align-items: center; gap: 0.5rem; user-select: none;
        }
        .captcha-input-small {
            width: 70px; padding: 0.5rem; font-size: 0.875rem; font-weight: 700; text-align: center;
            border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: all 0.2s;
            background: #fff; color: var(--text-main);
        }
        .captcha-input-small:focus { border-color: var(--accent-1); box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1); }
        .captcha-refresh {
            background: #fff; border: 1px solid #cbd5e1; cursor: pointer; color: var(--text-muted);
            padding: 0.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .captcha-refresh:hover { color: var(--text-main); background: #f1f5f9; }
        .captcha-refresh svg { width: 16px; height: 16px; }

        /* Success state */
        .modal-success {
            display: none; flex-direction: column; align-items: center; text-align: center; padding: 3rem 1rem;
        }
        .modal-success.show { display: flex; }
        .success-icon {
            width: 72px; height: 72px; border-radius: 50%; background: #dcfce7;
            border: 1px solid #bbf7d0;
            display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.15);
        }
        .success-icon svg { width: 36px; height: 36px; color: #16a34a; }
        .success-title { font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem; }
        .success-msg { font-size: 1rem; color: var(--text-muted); }
        .btn-close-success {
            margin-top: 2rem; padding: 0.875rem 2.5rem; border-radius: 10px; border: none;
            font-size: 0.875rem; font-weight: 600; cursor: pointer; background: var(--text-main); color: #fff;
            transition: all 0.2s;
        }
        .btn-close-success:hover { background: #334155; transform: translateY(-1px); }

        @media (max-width: 768px) {
            .hero { flex-direction: column; padding: 4rem 5%; gap: 4rem; text-align: center; }
            .hero-text { max-width: 100%; display: flex; flex-direction: column; align-items: center; }
            .hero-title { font-size: 2.5rem; }
            .visual-glass { width: 100%; max-width: 320px; }
            .navbar { padding: 1rem 5%; }
        }
    </style>
</head>
<body>

<div class="bg-glow"></div>

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
                    <rect x="1" y="1" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.4"/>
                    <rect x="9" y="1" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.4"/>
                    <rect x="1" y="9" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.4"/>
                    <rect x="9" y="9" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.4"/>
                </svg>
                Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="btn-nav">
                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 2H12.5C13.3 2 14 2.7 14 3.5v9c0 .8-.7 1.5-1.5 1.5H10" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    <path d="M6.5 11l3-3L6.5 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="9.5" y1="8" x2="2" y2="8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
                Masuk
            </a>
        @endauth
    </nav>

    {{-- ── Hero ── --}}
    <section class="hero">
        <div class="hero-text">
            <!-- <div class="hero-badge">
                <span class="dot"></span>
                Sistem Terintegrasi
            </div> -->
            <h1 class="hero-title">
                Selamat datang di<br>
                <em>Aplikasi Manajemen Radio</em>
            </h1>
            <p class="hero-sub-main">
                Kelola operasional siaran lebih cepat, efisien, dan terdokumentasi dengan baik.
            </p>
            <div class="hero-info-box">
                <div class="hero-info-text">
                    Ingin menjadi narasumber di program kami? Daftarkan data anda melalui tombol di bawah. Tim kami akan meninjau dan menghubungi anda.
                </div>
            </div>
            <button type="button" class="hero-cta-secondary" id="btnDaftarNarasumber">
                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1v14M1 8h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
                Daftar Sebagai Narasumber
            </button>
        </div>

        {{-- Radio visual: microphone + pulsing rings + sound wave bars --}}
        <div class="hero-visual">
            <div class="visual-glass">
                <div class="floating-orb orb-1"></div>
                <div class="floating-orb orb-2"></div>
                @if(!empty($isLive))
                <div class="live-badge">
                    <span class="live-dot"></span> LIVE
                </div>
                @endif
                <div class="mic-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="8" y="2" width="8" height="13" rx="4" stroke="currentColor" stroke-width="2" />
                        <line x1="8" y1="6" x2="16" y2="6" stroke="currentColor" stroke-width="2" />
                        <line x1="8" y1="10" x2="16" y2="10" stroke="currentColor" stroke-width="2" />
                        <path d="M5 11v1a7 7 0 0 0 14 0v-1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="12" y1="19" x2="12" y2="23" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="8" y1="23" x2="16" y2="23" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="sound-waves">
                    <div class="sound-wave"></div>
                    <div class="sound-wave"></div>
                    <div class="sound-wave"></div>
                    <div class="sound-wave"></div>
                    <div class="sound-wave"></div>
                    <div class="sound-wave"></div>
                    <div class="sound-wave"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Footer ── --}}
    <footer>
        &copy; {{ date('Y') }} {{ config('app.name', 'Radio RKB') }} — Aplikasi Manajemen Radio
    </footer>

</div>

{{-- ── Modal Pendaftaran Narasumber ── --}}
<div class="modal-overlay" id="modalNarasumber" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-box">

        {{-- Success State --}}
        <div class="modal-success" id="modalSuccess">
            <div class="success-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div class="success-title">Pendaftaran Berhasil!</div>
            <div class="success-msg" id="successMsg">Data Anda telah diterima dan akan segera diproses oleh tim kami.</div>
            <button type="button" class="btn-close-success" id="btnCloseSuccess">Tutup</button>
        </div>

        {{-- Form --}}
        <div id="modalFormWrapper">
            {{-- Header --}}
            <div class="modal-header">
                <div>
                    <div class="modal-title" id="modalTitle">Daftar Sebagai Narasumber</div>
                    <div class="modal-subtitle">Isi data di bawah untuk mendaftar sebagai narasumber siaran</div>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="modal-tabs" role="tablist">
                <button type="button" class="modal-tab active" id="tabByphone" role="tab" aria-selected="true" data-tipe="byphone">
                    📞 Narasumber Byphone
                </button>
                <button type="button" class="modal-tab" id="tabTalkshow" role="tab" aria-selected="false" data-tipe="talkshow">
                    🎙️ Narasumber Talkshow
                </button>
            </div>

            {{-- Tab Descriptions --}}
            <div class="tab-desc-box">
                <div id="descByphone" style="display: block;">
                    <strong>Narasumber Byphone:</strong> Wawancara dilakukan secara jarak jauh melalui panggilan telepon/seluler. Anda tidak perlu hadir secara fisik di studio radio.
                </div>
                <div id="descTalkshow" style="display: none;">
                    <strong>Narasumber Talkshow:</strong> Wawancara dilakukan secara tatap muka langsung di dalam studio. Anda akan hadir secara fisik di studio radio kami pada jadwal yang disepakati.
                </div>
            </div>

            {{-- Form Fields --}}
            <form id="formDaftarNarasumber" novalidate>
                @csrf
                <input type="hidden" name="tipe" id="fieldTipe" value="byphone">

                <div class="form-grid">
                    <div class="form-field">
                        <label class="form-label" for="fieldNama">Nama Lengkap <span style="color:#e05252">*</span></label>
                        <input type="text" class="form-input" id="fieldNama" name="nama" placeholder="Nama lengkap Anda" maxlength="150">
                        <span class="form-error" id="errNama"></span>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="fieldInstansi">Instansi / Organisasi <span style="color:#e05252">*</span></label>
                        <input type="text" class="form-input" id="fieldInstansi" name="instansi" placeholder="Nama instansi Anda" maxlength="150">
                        <span class="form-error" id="errInstansi"></span>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="fieldTelp">Nomor Telepon <span style="color:#e05252">*</span></label>
                        <input type="tel" class="form-input" id="fieldTelp" name="nomor_telp" placeholder="08xxxxxxxxxx" maxlength="20">
                        <span class="form-error" id="errTelp"></span>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="fieldTema">Tema Wawancara <span style="color:#e05252">*</span></label>
                        <input type="text" class="form-input" id="fieldTema" name="tema_wawancara" placeholder="Topik yang akan dibahas" maxlength="255">
                        <span class="form-error" id="errTema"></span>
                    </div>
                    <div class="form-field full">
                        <label class="form-label" for="fieldWaktu">Waktu Wawancara <span style="color:#e05252">*</span></label>
                        <div class="date-input-wrap" id="fieldWaktu-wrap" onclick="triggerDatePicker('fieldWaktu')">
                            <svg width="14" height="14" viewBox="0 0 15 15" fill="none"><rect x="1.5" y="2.5" width="12" height="11" rx="1.5" stroke="#8e8ab5" stroke-width="1.3"/><path d="M5 1v3M10 1v3M1.5 6h12" stroke="#8e8ab5" stroke-width="1.3" stroke-linecap="round"/></svg>
                            <span class="date-disp ph" id="fieldWaktu-lbl">Pilih tanggal &amp; waktu…</span>
                            <input type="datetime-local" id="fieldWaktu" name="waktu_wawancara" onchange="syncFormDatetime('fieldWaktu-lbl','fieldWaktu-wrap',this.value)">
                        </div>
                        <span class="form-error" id="errWaktu"></span>
                    </div>
                    <div class="form-field full">
                        <label class="form-label" for="fieldDraft">Draft Pertanyaan <span style="font-weight:400;color:var(--text-soft)">(opsional)</span></label>
                        <textarea class="form-textarea" id="fieldDraft" name="draft_pertanyaan" placeholder="Tuliskan poin-poin pertanyaan yang ingin dibahas..."></textarea>
                    </div>
                </div>

                <div class="captcha-row">
                    <div class="captcha-label-wrap">
                        <label class="form-label" style="margin-bottom:0;" for="fieldCaptcha">Verifikasi Keamanan <span style="color:#e05252">*</span></label>
                        <div style="font-size:11px; color:var(--text-soft); margin-top:2px;">Hitung angka untuk melanjutkan</div>
                    </div>
                    <div class="captcha-input-wrap">
                        <div class="captcha-box">
                            <span id="cap-n1">0</span> + <span id="cap-n2">0</span> =
                        </div>
                        <input type="number" class="captcha-input-small" id="fieldCaptcha" placeholder="?">
                        <button type="button" class="captcha-refresh" onclick="generateCaptcha()" title="Ganti Kode">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 2v6h-6M3 12a9 9 0 0 1 15-6.7L21 8M3 22v-6h6M21 12a9 9 0 0 1-15 6.7L3 16"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <span class="form-error" id="errCaptcha" style="display:block; margin-top:4px; text-align:right;"></span>

                <div class="form-error" id="errGeneral" style="margin-top:0.5rem;text-align:center;"></div>
                <div class="form-btns">
                    <button type="button" class="btn-cancel" id="btnCancelForm">Batal</button>
                    <button type="submit" class="btn-submit" id="btnSubmit">Kirim Pendaftaran</button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
const BULAN_ID = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
function syncFormDatetime(lblId, wrapId, val) {
    const lbl  = document.getElementById(lblId);
    const wrap = document.getElementById(wrapId);
    if (val) {
        const dt  = new Date(val);
        const tgl = dt.getDate() + '/' + BULAN_ID[dt.getMonth()] + '/' + dt.getFullYear();
        const jam = String(dt.getHours()).padStart(2,'0') + ':' + String(dt.getMinutes()).padStart(2,'0');
        lbl.textContent = tgl + ' ' + jam;
        lbl.classList.remove('ph');
        if (wrap) wrap.classList.add('has-value');
    } else {
        lbl.textContent = 'Pilih tanggal & waktu\u2026';
        lbl.classList.add('ph');
        if (wrap) wrap.classList.remove('has-value');
    }
}
function triggerDatePicker(inputId) {
    const inp = document.getElementById(inputId);
    if (inp) { if (inp.showPicker) inp.showPicker(); else inp.click(); }
}
(function () {
    const overlay      = document.getElementById('modalNarasumber');
    const btnOpen      = document.getElementById('btnDaftarNarasumber');
    const btnCloseSucc = document.getElementById('btnCloseSuccess');
    const form         = document.getElementById('formDaftarNarasumber');
    const fieldTipe    = document.getElementById('fieldTipe');
    const formWrapper  = document.getElementById('modalFormWrapper');
    const successPanel = document.getElementById('modalSuccess');
    const successMsg   = document.getElementById('successMsg');
    const btnSubmit    = document.getElementById('btnSubmit');
    const btnCancel    = document.getElementById('btnCancelForm');

    // Captcha Logic
    let captchaResult = 0;
    function generateCaptcha() {
        const n1 = Math.floor(Math.random() * 9) + 1;
        const n2 = Math.floor(Math.random() * 9) + 1;
        captchaResult = n1 + n2;
        document.getElementById('cap-n1').textContent = n1;
        document.getElementById('cap-n2').textContent = n2;
        document.getElementById('fieldCaptcha').value = '';
        document.getElementById('errCaptcha').textContent = '';
    }
    window.generateCaptcha = generateCaptcha;

    function openModal() {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        clearErrors();
        successPanel.classList.remove('show');
        formWrapper.style.display = '';
        form.reset();
        syncFormDatetime('fieldWaktu-lbl', 'fieldWaktu-wrap', '');
        fieldTipe.value = 'byphone';
        setActiveTab('byphone');
        generateCaptcha();
    }

    function closeModal() {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function clearErrors() {
        ['errNama','errInstansi','errTelp','errTema','errWaktu','errCaptcha','errGeneral'].forEach(function(id){
            var el = document.getElementById(id);
            if (el) el.textContent = '';
        });
        form.querySelectorAll('.form-input,.form-textarea').forEach(function(el){
            el.style.borderColor = '';
        });
    }

    function setActiveTab(tipe) {
        document.querySelectorAll('.modal-tab').forEach(function(tab){
            tab.classList.toggle('active', tab.dataset.tipe === tipe);
            tab.setAttribute('aria-selected', tab.dataset.tipe === tipe ? 'true' : 'false');
        });
        fieldTipe.value = tipe;

        const descByphone = document.getElementById('descByphone');
        const descTalkshow = document.getElementById('descTalkshow');
        if (descByphone && descTalkshow) {
            if (tipe === 'byphone') {
                descByphone.style.display = 'block';
                descTalkshow.style.display = 'none';
            } else {
                descByphone.style.display = 'none';
                descTalkshow.style.display = 'block';
            }
        }
    }

    // Tab click
    document.querySelectorAll('.modal-tab').forEach(function(tab){
        tab.addEventListener('click', function(){ setActiveTab(tab.dataset.tipe); });
    });

    // Open / Close
    btnOpen.addEventListener('click', openModal);
    btnCancel.addEventListener('click', closeModal);
    btnCloseSucc.addEventListener('click', closeModal);
    overlay.addEventListener('click', function(e){ if (e.target === overlay) closeModal(); });
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });

    // Form Submit
    form.addEventListener('submit', function(e){
        e.preventDefault();
        clearErrors();

        // Validate Captcha
        const userAns = parseInt(document.getElementById('fieldCaptcha').value);
        if (isNaN(userAns) || userAns !== captchaResult) {
            document.getElementById('errCaptcha').textContent = 'Jawaban salah. Silakan coba lagi.';
            generateCaptcha();
            return;
        }

        var data = new FormData(form);
        // datetime-local input sends "YYYY-MM-DDTHH:MM"; normalise to "YYYY-MM-DD HH:MM"
        var waktuField = document.getElementById('fieldWaktu');
        if (waktuField && waktuField.value) {
            data.set('waktu_wawancara', waktuField.value.replace('T', ' '));
        }
        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Mengirim…';

        fetch('{{ route('narasumber.self-register') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: data,
        })
        .then(function(res){ return res.json().then(function(json){ return { ok: res.ok, status: res.status, json: json }; }); })
        .then(function(result){
            if (result.ok && result.json.success) {
                successMsg.textContent = result.json.message;
                formWrapper.style.display = 'none';
                successPanel.classList.add('show');
            } else if (result.status === 422 && result.json.errors) {
                var map = {
                    'nama': 'errNama', 'instansi': 'errInstansi',
                    'nomor_telp': 'errTelp', 'tema_wawancara': 'errTema',
                    'waktu_wawancara': 'errWaktu',
                };
                Object.keys(result.json.errors).forEach(function(field){
                    var errId = map[field];
                    if (errId) {
                        var el = document.getElementById(errId);
                        if (el) el.textContent = result.json.errors[field][0];
                    } else {
                        var gen = document.getElementById('errGeneral');
                        if (gen) gen.textContent = result.json.errors[field][0];
                    }
                });
            } else {
                document.getElementById('errGeneral').textContent = 'Terjadi kesalahan. Silakan coba lagi.';
            }
        })
        .catch(function(){
            document.getElementById('errGeneral').textContent = 'Koneksi bermasalah. Silakan coba lagi.';
        })
        .finally(function(){
            btnSubmit.disabled = false;
            btnSubmit.textContent = 'Kirim Pendaftaran';
        });
    });
})();
</script>

</body>
</html>
