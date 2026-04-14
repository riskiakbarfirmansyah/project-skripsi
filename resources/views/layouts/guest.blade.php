<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:300,400,500,600,700|cormorant-garamond:400,500,600i&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

            :root {
                --sky:    #c8e0f8;
                --lavender: #cfc8f5;
                --violet: #b8a8f0;
                --peach:  #f5dfc8;
                --mint:   #c2eae4;
                --white:  #ffffff;
                --text-dark: #2d2a45;
                --text-mid:  #5c5880;
                --text-soft: #8e8ab5;
                --card-bg: rgba(255,255,255,0.52);
                --card-border: rgba(255,255,255,0.78);
                --input-bg: rgba(255,255,255,0.72);
                --input-border: rgba(180,170,240,0.35);
                --accent: #7c6ce0;
                --accent-soft: #a99df5;
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                min-height: 100vh;
                overflow: hidden;
            }

            /* ── Aurora Background ── */
            .aurora-bg {
                position: fixed;
                inset: 0;
                background: linear-gradient(145deg, #ddeeff 0%, #e8e2fa 30%, #f0e8ff 55%, #fce8d8 75%, #d8f0ec 100%);
                z-index: 0;
            }

            .aurora-bg::before,
            .aurora-bg::after {
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

            /* ── Noise grain overlay ── */
            .grain {
                position: fixed;
                inset: 0;
                z-index: 1;
                opacity: 0.028;
                background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
                pointer-events: none;
            }

            /* ── Layout ── */
            .auth-wrapper {
                position: relative;
                z-index: 2;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                padding: 2rem 1rem;
            }

            /* ── Card ── */
            .auth-card {
                width: 100%;
                max-width: 440px;
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


            /* ── Divider ── */
            .card-divider {
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(160,140,255,0.3), rgba(100,180,255,0.25), transparent);
                margin-bottom: 1.8rem;
            }

            /* ── Slot / form overrides ── */
            .auth-card label,
            .auth-card .block.font-medium {
                display: block;
                font-size: 0.75rem;
                font-weight: 600;
                letter-spacing: 0.07em;
                text-transform: uppercase;
                color: var(--text-mid);
                margin-bottom: 0.45rem;
            }
            

            .auth-card input[type="text"],
            .auth-card input[type="email"],
            .auth-card input[type="password"] {
                width: 100%;
                background: var(--input-bg);
                border: 1.5px solid var(--input-border);
                border-radius: 12px;
                color: var(--text-dark);
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 0.93rem;
                padding: 0.7rem 1rem;
                transition: border-color 0.25s, box-shadow 0.25s, background 0.2s;
                outline: none;
                backdrop-filter: blur(8px);
                box-shadow: 0 1px 0 rgba(255,255,255,0.8) inset, 0 2px 6px rgba(120,100,200,0.07);
            }

            .auth-card input:focus {
                border-color: rgba(140,110,240,0.55);
                background: rgba(255,255,255,0.88);
                box-shadow:
                    0 0 0 4px rgba(140,110,240,0.1),
                    0 1px 0 rgba(255,255,255,0.9) inset;
            }

            .auth-card input::placeholder {
                color: rgba(130,120,180,0.45);
            }

            .card-logo {
                display: flex;
                justify-content: center;
                align-items: center;
                margin-bottom: 1.2rem;
            }

            /* Primary button */
            .auth-card button[type="submit"],
            .auth-card .btn-primary {
                background: linear-gradient(135deg, #9b8af4 0%, #7ab3f5 100%);
                border: none;
                border-radius: 12px;
                color: #fff;
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 0.9rem;
                font-weight: 600;
                padding: 0.72rem 1.6rem;
                cursor: pointer;
                letter-spacing: 0.03em;
                box-shadow:
                    0 4px 16px rgba(140,110,240,0.3),
                    0 1px 0 rgba(255,255,255,0.25) inset;
                transition: transform 0.18s, box-shadow 0.2s, opacity 0.18s;
            }

            /* container forgot password + login */
            .auth-card .flex.items-center.justify-end.mt-4{
                display:flex;
                flex-direction:column;      /* elemen ditumpuk vertikal */
                align-items:flex-start;     /* forgot password di kiri */
                gap:20px;                   /* jarak antar elemen */
            }

            /* tombol login di tengah */
            .auth-card button[type="submit"]{
                align-self:center;          /* tombol ke tengah */
            }

            .auth-card button[type="submit"]:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 24px rgba(140,110,240,0.38), 0 1px 0 rgba(255,255,255,0.25) inset;
            }

            .auth-card button[type="submit"]:active {
                transform: translateY(0);
            }

            /* Links */
            .auth-card a {
                color: var(--accent);
                text-decoration: none;
                font-size: 0.875rem;
                font-weight: 500;
                transition: color 0.2s;
            }

            .auth-card a:hover {
                color: var(--accent-soft);
            }

            /* Checkbox */
            .auth-card input[type="checkbox"] {
                accent-color: var(--accent);
            }

            /* Error */
            .auth-card .text-red-600,
            .auth-card [class*="text-red"] {
                color: #e05c7a !important;
                font-size: 0.78rem;
            }

            /* Status/success messages */
            .auth-card .text-green-600,
            .auth-card [class*="text-green"] {
                color: #3aab8c !important;
                font-size: 0.82rem;
            }

            /* Spacing helpers */
            .auth-card .mt-4 { margin-top: 1.4rem; }
            .auth-card .mt-6 { margin-top: 1.5rem; }
        </style>
    </head>

    <body>
        <!-- Aurora background layers -->
        <div class="aurora-bg"></div>
        <div class="orb-mid"></div>
        <div class="grain"></div>

        <div class="auth-wrapper">
            <div class="auth-card">
                <!-- Logo -->
                <div class="card-logo">
                    <a href="/" style="display:flex;align-items:center;gap:0.75rem;text-decoration:none;">
                        <x-application-logo />
                    </a>
                </div>
                <div class="card-divider"></div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>