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

        /* ── Modal Overlay ── */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 100;
            background: rgba(45, 42, 69, 0.45);
            backdrop-filter: blur(6px);
            display: flex; align-items: center; justify-content: center;
            padding: 1rem;
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s;
        }
        .modal-overlay.active {
            opacity: 1; pointer-events: all;
        }
        .modal-box {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(28px) saturate(1.8);
            border: 1.5px solid rgba(255,255,255,0.85);
            border-radius: 24px;
            padding: 2rem 2.2rem;
            width: 100%; max-width: 520px;
            box-shadow: 0 16px 60px rgba(120,100,200,0.18), 0 2px 0 rgba(255,255,255,0.9) inset;
            transform: translateY(24px) scale(0.97);
            transition: transform 0.28s cubic-bezier(0.22,1,0.36,1);
        }
        .modal-overlay.active .modal-box {
            transform: translateY(0) scale(1);
        }
        .modal-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            margin-bottom: 1.4rem;
        }
        .modal-title {
            font-size: 1.15rem; font-weight: 800; color: var(--text-dark);
        }
        .modal-subtitle {
            font-size: 0.82rem; color: var(--text-soft); margin-top: 0.2rem;
        }
        .modal-close {
            background: none; border: none; cursor: pointer;
            color: var(--text-soft); padding: 0.2rem;
            line-height: 1; font-size: 1.4rem;
            transition: color 0.15s;
        }
        .modal-close:hover { color: var(--text-dark); }

        /* Tabs */
        .modal-tabs {
            display: flex; gap: 0.4rem;
            background: rgba(124,108,224,0.07);
            border-radius: 12px; padding: 0.3rem;
            margin-bottom: 1.4rem;
        }
        .modal-tab {
            flex: 1; padding: 0.55rem 0.5rem;
            border: none; border-radius: 9px;
            font-family: inherit; font-size: 0.85rem; font-weight: 600;
            cursor: pointer; background: transparent;
            color: var(--text-soft);
            transition: background 0.18s, color 0.18s;
        }
        .modal-tab.active {
            background: #fff;
            color: var(--accent);
            box-shadow: 0 2px 8px rgba(124,108,224,0.15);
        }

        /* Form */
        .form-grid {
            display: grid; grid-template-columns: 1fr; gap: 0.85rem;
        }
        .form-field { display: flex; flex-direction: column; gap: 0.3rem; }
        .form-label {
            font-size: 0.78rem; font-weight: 600; color: var(--text-mid);
        }
        .form-input, .form-textarea {
            padding: 0.6rem 0.85rem;
            border: 1.5px solid rgba(124,108,224,0.2);
            border-radius: 10px;
            font-family: inherit; font-size: 0.88rem; color: var(--text-dark);
            background: rgba(255,255,255,0.7);
            transition: border-color 0.18s, box-shadow 0.18s;
            outline: none;
        }
        .form-input:focus, .form-textarea:focus {
            border-color: rgba(124,108,224,0.55);
            box-shadow: 0 0 0 3px rgba(124,108,224,0.1);
        }
        .form-textarea { resize: vertical; min-height: 72px; }
        .form-error { font-size: 0.75rem; color: #e05252; }

        .btn-submit {
            width: 100%; margin-top: 1.2rem;
            padding: 0.82rem; border-radius: 12px; border: none;
            font-family: inherit; font-size: 0.95rem; font-weight: 700;
            cursor: pointer;
            background: linear-gradient(135deg, #9b8af4 0%, #7ab3f5 100%);
            color: #fff;
            box-shadow: 0 4px 18px rgba(140,110,240,0.3);
            transition: transform 0.18s, box-shadow 0.2s;
        }
        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(140,110,240,0.38);
        }
        .btn-submit:disabled { opacity: 0.65; cursor: not-allowed; }

        /* Success state */
        .modal-success {
            display: none; flex-direction: column; align-items: center;
            text-align: center; padding: 1rem 0;
        }
        .modal-success.show { display: flex; }
        .success-icon {
            width: 56px; height: 56px; border-radius: 50%;
            background: linear-gradient(135deg, #a8f0c6 0%, #6ee0b5 100%);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 4px 20px rgba(100,210,170,0.35);
        }
        .success-icon svg { width: 28px; height: 28px; color: #1a7a56; }
        .success-title { font-size: 1.05rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.4rem; }
        .success-msg { font-size: 0.85rem; color: var(--text-mid); line-height: 1.6; }
        .btn-close-success {
            margin-top: 1.2rem;
            padding: 0.65rem 2rem; border-radius: 10px; border: none;
            font-family: inherit; font-size: 0.9rem; font-weight: 600;
            cursor: pointer;
            background: linear-gradient(135deg, #9b8af4 0%, #7ab3f5 100%);
            color: #fff;
            box-shadow: 0 4px 14px rgba(140,110,240,0.28);
        }

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
            <button type="button" class="hero-cta-secondary" id="btnDaftarNarasumber">
                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1v14M1 8h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
                Daftar Sebagai Narasumber
            </button>
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

{{-- ── Modal Pendaftaran Narasumber ── --}}
<div class="modal-overlay" id="modalNarasumber" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-box">

        {{-- Header --}}
        <div class="modal-header">
            <div>
                <div class="modal-title" id="modalTitle">Daftar Sebagai Narasumber</div>
                <div class="modal-subtitle">Isi data di bawah untuk mendaftar sebagai narasumber siaran</div>
            </div>
            <button class="modal-close" id="btnCloseModal" aria-label="Tutup">&times;</button>
        </div>

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
            {{-- Tabs --}}
            <div class="modal-tabs" role="tablist">
                <button type="button" class="modal-tab active" id="tabByphone" role="tab" aria-selected="true" data-tipe="byphone">
                    📞 Narasumber Byphone
                </button>
                <button type="button" class="modal-tab" id="tabTalkshow" role="tab" aria-selected="false" data-tipe="talkshow">
                    🎙️ Narasumber Talkshow
                </button>
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
                        <input type="datetime-local" class="form-input" id="fieldWaktu" name="waktu_wawancara">
                        <span class="form-error" id="errWaktu"></span>
                    </div>
                    <div class="form-field full">
                        <label class="form-label" for="fieldDraft">Draft Pertanyaan <span style="font-weight:400;color:var(--text-soft)">(opsional)</span></label>
                        <textarea class="form-textarea" id="fieldDraft" name="draft_pertanyaan" placeholder="Tuliskan poin-poin pertanyaan yang ingin dibahas..."></textarea>
                    </div>
                </div>

                <div class="form-error" id="errGeneral" style="margin-top:0.5rem;text-align:center;"></div>
                <button type="submit" class="btn-submit" id="btnSubmit">
                    Kirim Pendaftaran
                </button>
            </form>
        </div>

    </div>
</div>

<script>
(function () {
    const overlay      = document.getElementById('modalNarasumber');
    const btnOpen      = document.getElementById('btnDaftarNarasumber');
    const btnClose     = document.getElementById('btnCloseModal');
    const btnCloseSucc = document.getElementById('btnCloseSuccess');
    const form         = document.getElementById('formDaftarNarasumber');
    const fieldTipe    = document.getElementById('fieldTipe');
    const formWrapper  = document.getElementById('modalFormWrapper');
    const successPanel = document.getElementById('modalSuccess');
    const successMsg   = document.getElementById('successMsg');
    const btnSubmit    = document.getElementById('btnSubmit');

    function openModal() {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        clearErrors();
        successPanel.classList.remove('show');
        formWrapper.style.display = '';
        form.reset();
        fieldTipe.value = 'byphone';
        setActiveTab('byphone');
    }

    function closeModal() {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function clearErrors() {
        ['errNama','errInstansi','errTelp','errTema','errWaktu','errGeneral'].forEach(function(id){
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
    }

    // Tab click
    document.querySelectorAll('.modal-tab').forEach(function(tab){
        tab.addEventListener('click', function(){ setActiveTab(tab.dataset.tipe); });
    });

    // Open / Close
    btnOpen.addEventListener('click', openModal);
    btnClose.addEventListener('click', closeModal);
    btnCloseSucc.addEventListener('click', closeModal);
    overlay.addEventListener('click', function(e){ if (e.target === overlay) closeModal(); });
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });

    // Form Submit
    form.addEventListener('submit', function(e){
        e.preventDefault();
        clearErrors();

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
