<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Penyiar @yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('/image/LOGO.png') }}">
    <link rel="shortcut icon" href="{{ asset('/image/LOGO.png') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            font-size: 14px;
            line-height: 1.5;
        }

        /* ── Sidebar ─────────────────────────────────── */
        .sb {
            position: fixed; top: 0; left: 0;
            height: 100vh; width: 224px;
            background: #fff;
            border-right: 1px solid #e2e8f0;
            display: flex; flex-direction: column;
            z-index: 100;
        }
        .sb-logo {
            padding: 18px 16px 14px;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; gap: 10px;
        }
        .sb-ico {
            width: 32px; height: 32px; border-radius: 8px;
            background: #059669;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sb-ico svg { width: 16px; height: 16px; }
        .sb-name { font-size: 14px; font-weight: 700; color: #0f172a; }
        .sb-role { font-size: 10px; color: #94a3b8; margin-top: 1px; letter-spacing: .03em; }
        .sb-nav { padding: 10px 8px; flex: 1; overflow-y: auto; }
        .nav-lbl {
            font-size: 10px; color: #94a3b8;
            letter-spacing: .08em; padding: 0 8px;
            margin: 10px 0 4px; text-transform: uppercase;
        }
        .ni {
            display: flex; align-items: center; gap: 9px;
            padding: 8px 10px; border-radius: 8px;
            cursor: pointer; font-size: 13px; color: #475569;
            border: none; background: transparent;
            width: 100%; text-align: left; margin-bottom: 2px;
            text-decoration: none; transition: background .12s, color .12s;
        }
        .ni:hover { background: #ecfdf5; color: #059669; }
        .ni.active { background: #d1fae5; color: #065f46; font-weight: 600; }
        .ni svg { width: 15px; height: 15px; flex-shrink: 0; }
        .ni .bdg {
            margin-left: auto; font-size: 10px;
            padding: 1px 7px; border-radius: 20px;
            background: #d1fae5; color: #065f46; font-weight: 700;
        }
        .sb-foot {
            padding: 12px 14px;
            border-top: 1px solid #e2e8f0;
            display: flex; align-items: center; gap: 9px;
        }
        .av {
            width: 30px; height: 30px; border-radius: 50%;
            background: #ecfdf5;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 700; color: #059669; flex-shrink: 0;
        }
        .logout-btn {
            margin-left: auto; background: none; border: none;
            cursor: pointer; color: #94a3b8; padding: 4px;
            border-radius: 6px; transition: color .15s, background .15s;
        }
        .logout-btn:hover { color: #ef4444; background: #fef2f2; }
        .logout-btn svg { width: 14px; height: 14px; }

        /* ── Main ─────────────────────────────────────── */
        .main { margin-left: 224px; min-height: 100vh; }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 13px 24px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .tb-title { font-size: 15px; font-weight: 700; color: #0f172a; }
        .tb-sub { font-size: 14px; font-weight: 600; color: #1e293b; }
        .content { padding: 24px; }

        /* ── Sidebar stats ────────────────────────────── */
        .sb-stats {
            padding: 10px 10px 6px;
            border-top: 1px solid #e2e8f0;
        }
        .sb-stats-title {
            font-size: 9px; font-weight: 700; color: #94a3b8;
            text-transform: uppercase; letter-spacing: .1em;
            padding: 0 4px 6px;
        }
        .sb-stats-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 6px;
        }
        .sb-sc {
            background: #ecfdf5; border-radius: 8px;
            padding: 10px 10px 8px; text-align: center;
            border: 1px solid #d1fae5;
            transition: transform .15s, box-shadow .15s;
        }
        .sb-sc:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(5,150,105,.1);
        }
        .sb-sc-ico {
            width: 24px; height: 24px; border-radius: 6px;
            display: inline-flex; align-items: center; justify-content: center;
            margin: 0 auto 5px;
        }
        .sb-sc-ico svg { width: 12px; height: 12px; }
        .sb-sc-val { font-size: 18px; font-weight: 800; color: #0f172a; line-height: 1.1; }
        .sb-sc-lbl { font-size: 9px; color: #64748b; margin-top: 2px; font-weight: 600; letter-spacing: .02em; }

        /* ── Tabs ─────────────────────────────────────── */
        .tabs {
            display: flex; gap: 4px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }
        .tab-btn {
            padding: 9px 16px; font-size: 13px; font-weight: 600;
            border: none; background: none; cursor: pointer;
            color: #64748b; border-bottom: 2px solid transparent;
            margin-bottom: -1px; transition: color .15s, border-color .15s;
        }
        .tab-btn:hover { color: #059669; }
        .tab-btn.active { color: #059669; border-bottom-color: #059669; }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        /* ── Table card ───────────────────────────────── */
        .tc {
            background: #fff; border-radius: 10px;
            border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 20px;
        }
        .tbar {
            display: flex; align-items: center; gap: 9px;
            padding: 13px 16px; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap;
        }
        .sw { position: relative; flex: 1; min-width: 140px; max-width: 280px; }
        .sw svg {
            position: absolute; left: 9px; top: 50%; transform: translateY(-50%);
            width: 13px; height: 13px; color: #94a3b8;
        }
        .sw input {
            width: 100%; padding: 7px 9px 7px 28px;
            font-size: 13px; border: 1px solid #cbd5e1;
            border-radius: 7px; outline: none; color: #0f172a;
        }
        .sw input:focus { border-color: #6ee7b7; box-shadow: 0 0 0 3px #ecfdf5; }
        select.fsel {
            padding: 7px 9px; font-size: 12px;
            border: 1px solid #cbd5e1; border-radius: 7px;
            outline: none; color: #374151; background: #fff;
        }
        .rc { margin-left: auto; font-size: 12px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 9px 14px; text-align: left;
            font-size: 11px; font-weight: 700; color: #64748b;
            background: #f8fafc; border-bottom: 1px solid #e2e8f0;
            text-transform: uppercase; letter-spacing: .05em;
            white-space: nowrap;
        }
        tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f8fafc; }
        tbody td { padding: 10px 14px; font-size: 13px; color: #0f172a; }
        .mono { font-size: 11px; font-family: 'Courier New', monospace; color: #64748b; }
        .trunc { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; }
        .ini {
            width: 28px; height: 28px; border-radius: 50%;
            background: #ecfdf5;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 700; color: #059669; flex-shrink: 0;
        }
        .abt { display: flex; gap: 5px; }
        .bico {
            width: 28px; height: 28px; border-radius: 6px;
            border: 1px solid #e2e8f0; background: transparent;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            transition: background .1s, border-color .1s;
        }
        .bico:hover { background: #f1f5f9; }
        .bico svg { width: 12px; height: 12px; color: #475569; }
        .pag {
            padding: 11px 16px; border-top: 1px solid #e2e8f0;
            font-size: 12px; color: #64748b;
            display: flex; flex-direction: column; align-items: flex-start; gap: 8px;
        }
        .pagination-container { display: flex; align-items: center; gap: 4px; }
        .page-btn {
            padding: 4px 10px; border: 1px solid #e2e8f0; border-radius: 6px;
            background: #fff; color: #475569; font-size: 12px; cursor: pointer;
            transition: all 0.15s;
        }
        .page-btn:hover:not(.active) { background: #f8fafc; border-color: #cbd5e1; }
        .page-btn.active { background: #ea580c; color: #fff; border-color: #ea580c; cursor: default; }
        .page-dots { padding: 4px 6px; color: #94a3b8; }
        .pag-info-text { font-size: 12px; color: #64748b; }
        .empty { text-align: center; padding: 40px; color: #94a3b8; font-size: 13px; }

        /* ── Modals ───────────────────────────────────── */
        .mbg {
            display: none; position: fixed; inset: 0;
            background: rgba(15,23,42,.45);
            align-items: flex-start; justify-content: center;
            padding: 48px 14px; z-index: 200; overflow-y: auto;
        }
        .mbg.open { display: flex; }
        .modal {
            background: #fff; border-radius: 12px;
            border: 1px solid #e2e8f0; padding: 24px;
            width: 500px; max-width: 98vw;
        }
        .mttl {
            font-size: 15px; font-weight: 700; margin-bottom: 18px; color: #0f172a;
            display: flex; align-items: center; justify-content: space-between;
        }
        .mclose {
            background: none; border: none; cursor: pointer;
            color: #94a3b8; font-size: 18px; padding: 0; line-height: 1;
        }
        .mclose:hover { color: #0f172a; }
        .fg { margin-bottom: 13px; }
        .fl { font-size: 12px; color: #475569; margin-bottom: 5px; display: block; font-weight: 500; }
        .fi {
            width: 100%; padding: 8px 11px; font-size: 13px;
            border: 1px solid #cbd5e1; border-radius: 7px; outline: none; color: #0f172a;
        }
        .fi:focus { border-color: #6ee7b7; box-shadow: 0 0 0 3px #ecfdf5; }
        .fi.is-invalid { border-color: #f87171; }
        textarea.fi { resize: vertical; }
        .frow { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .mft { display: flex; justify-content: flex-end; gap: 8px; margin-top: 18px; }
        .bcn {
            padding: 8px 16px; font-size: 13px; border-radius: 7px;
            border: 1px solid #cbd5e1; background: transparent;
            cursor: pointer; color: #374151;
        }
        .bcn:hover { background: #f1f5f9; }
        .bsv {
            padding: 8px 16px; font-size: 13px; border-radius: 7px;
            border: none; color: #fff; cursor: pointer; font-weight: 600;
            background: #059669;
        }
        .bsv:hover { opacity: .9; }

        /* ── Detail modal ────────────────────────────── */
        .dr { display: flex; gap: 10px; margin-bottom: 9px; }
        .dl { font-size: 12px; color: #64748b; width: 135px; flex-shrink: 0; font-weight: 500; }
        .dv { font-size: 13px; color: #0f172a; }
        .dbox {
            font-size: 13px; color: #374151;
            background: #f8fafc; border-radius: 8px;
            padding: 10px 13px; line-height: 1.65;
            white-space: pre-wrap; margin-top: 5px;
        }

        /* ── Alert / Toast ────────────────────────────── */
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            border-left: 4px solid;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            z-index: 300;
            animation: slideIn .3s ease-out;
            max-width: 380px;
            min-width: 260px;
        }
        .alert.hide {
            animation: slideOut .3s ease-in forwards;
        }
        .alert > svg { flex-shrink: 0; margin-top: 1px; }
        .alert-body { flex: 1; min-width: 0; }
        .alert-title { font-size: 13px; font-weight: 700; line-height: 1.3; margin-bottom: 2px; }
        .alert-msg { font-size: 12px; opacity: 0.8; line-height: 1.4; }
        .alert-close {
            background: none; border: none; cursor: pointer;
            padding: 2px; border-radius: 4px; color: inherit;
            opacity: 0.5; transition: opacity .15s; flex-shrink: 0; margin-top: 1px;
        }
        .alert-close:hover { opacity: 1; }
        .alert-close svg { width: 12px; height: 12px; }
        @keyframes slideIn {
            from { transform: translateX(440px); opacity: 0; }
            to   { transform: translateX(0);     opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0);     opacity: 1; }
            to   { transform: translateX(440px); opacity: 0; }
        }
        .alert-success {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            color: #166534;
            border-left-color: #10b981;
        }
        .alert-error {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #991b1b;
            border-left-color: #ef4444;
        }
        .alert svg { width: 18px; height: 18px; flex-shrink: 0; }

        /* ── Delete Confirmation Modal ───────────────── */
        .mbg-center { align-items: center; }
        .del-icon-wrap {
            width: 56px; height: 56px; border-radius: 50%;
            background: #fef2f2;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }
        .del-modal-title {
            font-size: 16px; font-weight: 700; color: #0f172a;
            margin-bottom: 8px; text-align: center;
        }
        .del-modal-msg {
            font-size: 13px; color: #64748b;
            line-height: 1.6; text-align: center;
        }
        .bdel { background: #dc2626; }
        .bdel:disabled { opacity: .7; cursor: not-allowed; }
        @keyframes modalIn {
            from { opacity: 0; transform: scale(.95) translateY(-8px); }
            to   { opacity: 1; transform: scale(1)   translateY(0); }
        }
        .modal-anim { animation: modalIn .2s ease-out; }

        @media (max-width: 768px) {
            .sb { transform: translateX(-224px); transition: transform .25s ease; }
            .sb.open { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,.15); }
            .main { margin-left: 0; }
            .content { padding: 14px; }
            .topbar { padding: 10px 14px; }
            .tb-title { font-size: 13px; }
            .tb-sub { font-size: 12px; }
            .tbar { gap: 6px; padding: 10px 12px; }
            .sw { min-width: 100px; max-width: 100%; }
            .btn-add { padding: 6px 10px; font-size: 11px; }
            .btn-add svg { width: 11px; height: 11px; }
            .fsel { font-size: 11px; padding: 6px 7px; }
            .modal { width: 95vw; padding: 16px; }
            .frow { grid-template-columns: 1fr; }
            table { table-layout: auto; }
            thead th { font-size: 10px; padding: 7px 8px; }
            tbody td { font-size: 12px; padding: 8px; }
            .pag { padding: 8px 12px; font-size: 11px; }
            .rc { font-size: 11px; }
        }
        @media (max-width: 480px) {
            .tbar { flex-direction: column; align-items: stretch; }
            .sw { max-width: 100%; }
            .rc { margin-left: 0; }
            .btn-add { justify-content: center; width: 100%; }
        }

        /* ── Hamburger + Overlay ───────────────────────── */
        .sb-toggle {
            display: none; background: none; border: none;
            cursor: pointer; padding: 6px; border-radius: 6px;
            color: #475569; transition: background .15s;
        }
        .sb-toggle:hover { background: #f1f5f9; }
        .sb-toggle svg { width: 20px; height: 20px; }
        .sb-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(15,23,42,.4); z-index: 99;
            opacity: 0; transition: opacity .25s;
        }
        .sb-overlay.active { display: block; opacity: 1; }
        @media (max-width: 768px) {
            .sb-toggle { display: flex; align-items: center; justify-content: center; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══════════════════ SIDEBAR ═══════════════════ --}}
<div class="sb">
    <div class="sb-logo">
        <div class="sb-ico" style="background: transparent;">
            <img src="{{ asset('/image/LOGO.png') }}" alt="Logo" style="width: 32px; height: 32px; object-fit: contain; border-radius: 8px;">
        </div>
        <div>
            <div class="sb-name">Penyiar</div>
            <div class="sb-role">Panel data narasumber</div>
        </div>
    </div>

    <div class="sb-nav">
        <a href="{{ route('penyiar.dashboard') }}#byphone"
           class="ni" onclick="showTab('byphone');return false;">
            <svg viewBox="0 0 15 15" fill="none">
                <path d="M5 1.5C4.5 1.5 4 2 4 2.5L3 5c-.5 1 0 2 1 2.5 1.5 1 3.5 3 4.5 4.5.5 1 1.5 1.5 2.5 1L13 12c.5-.5.5-1.5 0-2L11 8.5c-.5-.5-1.5-.5-2 0l-.5.5C8 8.5 7 7.5 6.5 7l.5-.5c.5-.5.5-1.5 0-2L5.5 2c-.5-.5-1-.5-0.5-.5z"
                      stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
            </svg>
            Narasumber Byphone
            <span class="bdg">{{ $stats['byphone_hari_ini'] ?? 0 }}</span>
        </a>
        <a href="{{ route('penyiar.dashboard') }}#talkshow"
           class="ni" onclick="showTab('talkshow');return false;">
            <svg viewBox="0 0 15 15" fill="none">
                <rect x="1.5" y="1.5" width="12" height="12" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
                <path d="M4.5 8l2 2 4-4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Narasumber Talkshow
            <span class="bdg">{{ $stats['talkshow_hari_ini'] ?? 0 }}</span>
        </a>
    </div>

    @yield('sidebar-stats')

    <div class="sb-foot">
        <div class="av">{{ strtoupper(substr(auth()->user()->name ?? 'PY', 0, 2)) }}</div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:12px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                {{ auth()->user()->name ?? 'Penyiar' }}
            </div>
            <div style="font-size:10px;color:#94a3b8">Penyiar</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="logout-btn" title="Logout">
                <svg viewBox="0 0 15 15" fill="none">
                    <path d="M9 2H11.5C12.3 2 13 2.7 13 3.5v8c0 .8-.7 1.5-1.5 1.5H9" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                    <path d="M6 10l3-2.5L6 5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="9" y1="7.5" x2="2" y2="7.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                </svg>
            </button>
        </form>
    </div>
</div>

<div class="sb-overlay" id="sb-overlay" onclick="toggleSidebar()"></div>

{{-- ═══════════════════ MAIN ═══════════════════ --}}
<div class="main">
    <div class="topbar">
        <div style="display:flex;align-items:center;gap:10px;">
            <button class="sb-toggle" id="sb-toggle" onclick="toggleSidebar()" title="Menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div>
                <div class="tb-title" id="pg-title">@yield('page-title', 'Dashboard')</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="font-size:12px;color:#64748b;" id="clk"></div>
            <div style="position:relative;" id="tb-profile-wrap">
                <div class="av" style="cursor:pointer;" onclick="toggleTbMenu()" title="Menu Profil">
                    {{ strtoupper(substr(auth()->user()->name ?? 'PY', 0, 2)) }}
                </div>
                <div id="tb-profile-menu" style="display:none;position:absolute;right:0;top:38px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:4px;min-width:175px;box-shadow:0 4px 16px rgba(15,23,42,.12);z-index:300;">
                    <div style="padding:8px 10px;border-bottom:1px solid #f1f5f9;margin-bottom:4px;">
                        <div style="font-size:12px;font-weight:700;color:#0f172a;">{{ auth()->user()->name }}</div>
                        <div style="font-size:11px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->email }}</div>
                    </div>
                    <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:7px;padding:7px 10px;border-radius:6px;font-size:12px;color:#374151;text-decoration:none;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                        <svg width="13" height="13" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="4.5" r="2.5" stroke="currentColor" stroke-width="1.3"/><path d="M2 13c0-3 2.5-5 5.5-5s5.5 2 5.5 5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" style="display:flex;align-items:center;gap:7px;padding:7px 10px;border-radius:6px;font-size:12px;color:#ef4444;background:none;border:none;cursor:pointer;width:100%;text-align:left;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                            <svg width="13" height="13" viewBox="0 0 15 15" fill="none"><path d="M9 2H11.5C12.3 2 13 2.7 13 3.5v8c0 .8-.7 1.5-1.5 1.5H9" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><path d="M6 10l3-2.5L6 5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/><line x1="9" y1="7.5" x2="2" y2="7.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success" id="session-flash-success">
                <svg viewBox="0 0 18 18" fill="none">
                    <circle cx="9" cy="9" r="8" stroke="#059669" stroke-width="1.5"/>
                    <path d="M5.5 9l2.5 2.5 4.5-5" stroke="#059669" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                {{ session('success') }}
            </div>
            <script>
                (function(){
                    var el = document.getElementById('session-flash-success');
                    setTimeout(function(){
                        el.classList.add('hide');
                        setTimeout(function(){ el.style.display='none'; }, 300);
                    }, 4000);
                })();
            </script>
        @endif
        @if(session('error'))
            <div class="alert alert-error" id="session-flash-error">
                <svg viewBox="0 0 18 18" fill="none">
                    <circle cx="9" cy="9" r="8" stroke="#dc2626" stroke-width="1.5"/>
                    <path d="M9 5.5v4M9 12v.5" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                {{ session('error') }}
            </div>
            <script>
                (function(){
                    var el = document.getElementById('session-flash-error');
                    setTimeout(function(){
                        el.classList.add('hide');
                        setTimeout(function(){ el.style.display='none'; }, 300);
                    }, 4000);
                })();
            </script>
        @endif
        <div id="flash-msg" style="display:none;"></div>

        @yield('content')
    </div>
</div>

{{-- ═══════════════════ DELETE CONFIRM MODAL ═══════════════════ --}}
<div class="mbg mbg-center" id="m-delete-confirm">
    <div class="modal modal-anim" style="width:420px;max-width:95vw;">
        <div style="padding:8px 0 4px;">
            <div class="del-icon-wrap">
                <svg width="26" height="26" viewBox="0 0 15 15" fill="none">
                    <path d="M2.5 4.5h10M6 4.5V3h3v1.5M5.5 4.5v7h4v-7" stroke="#dc2626" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="del-modal-title" id="del-modal-title">Hapus Data</div>
            <div class="del-modal-msg" id="del-modal-msg">Apakah Anda yakin ingin menghapus data ini?</div>
        </div>
        <div class="mft" style="margin-top:22px;">
            <button class="bcn" onclick="closeDeleteConfirm()">Batalkan</button>
            <button class="bsv bdel" id="del-modal-btn" onclick="confirmDelete()">
                <span id="del-modal-btn-text">Hapus</span>
            </button>
        </div>
    </div>
</div>

<script>
function toggleTbMenu() {
    const m = document.getElementById('tb-profile-menu');
    m.style.display = m.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    const w = document.getElementById('tb-profile-wrap');
    if (w && !w.contains(e.target)) {
        const m = document.getElementById('tb-profile-menu');
        if (m) m.style.display = 'none';
    }
});

function clock() {
    const now = new Date();
    document.getElementById('clk').textContent =
        now.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' }) + ' · ' +
        now.toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' });
}
clock(); setInterval(clock, 10000);

function ini(name) {
    return (name || '?').split(' ')
        .filter(w => /[A-Za-z]/.test(w))
        .slice(0, 2)
        .map(w => w[0].toUpperCase())
        .join('');
}

const CSRF = document.querySelector('meta[name="csrf-token"]').content;
function apiFetch(url, method = 'GET', body = null) {
    const opts = {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    };
    if (body) opts.body = JSON.stringify(body);
    return fetch(url, opts).then(r => r.json());
}

function showFlash(msg, type = 'success') {
    const el = document.getElementById('flash-msg');
    const isSuccess = type === 'success';
    const iconSvg = isSuccess
        ? '<svg viewBox="0 0 18 18" fill="none" width="20" height="20"><circle cx="9" cy="9" r="8" stroke="#059669" stroke-width="1.5"/><path d="M5.5 9l2.5 2.5 4.5-5" stroke="#059669" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
        : '<svg viewBox="0 0 18 18" fill="none" width="20" height="20"><circle cx="9" cy="9" r="8" stroke="#dc2626" stroke-width="1.5"/><path d="M9 5.5v4M9 12v.5" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round"/></svg>';
    const closeSvg = '<svg viewBox="0 0 15 15" fill="none" width="12" height="12"><path d="M3 3l9 9M12 3l-9 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
    el.className = 'alert alert-' + type;
    el.classList.remove('hide');
    el.style.display = 'flex';
    el.innerHTML = iconSvg + '<div class="alert-body"><div class="alert-title"></div><div class="alert-msg"></div></div><button class="alert-close">' + closeSvg + '</button>';
    el.querySelector('.alert-title').textContent = isSuccess ? 'Berhasil' : 'Gagal';
    el.querySelector('.alert-msg').textContent = msg;
    el.querySelector('.alert-close').addEventListener('click', function() {
        el.classList.add('hide');
        setTimeout(() => { el.style.display = 'none'; }, 300);
    });
    clearTimeout(showFlash._timer);
    showFlash._timer = setTimeout(() => {
        el.classList.add('hide');
        setTimeout(() => { el.style.display = 'none'; }, 300);
    }, 4000);
}

function showTab(name) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    document.getElementById('tab-' + name + '-btn').classList.add('active');
}

function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}

document.querySelectorAll('.mbg').forEach(bg => {
    bg.addEventListener('click', function(e) {
        if (e.target === this) {
            if (this.id === 'm-delete-confirm') closeDeleteConfirm();
            else closeModal(this.id);
        }
    });
});

// ── Delete Confirmation Modal ─────────────────────────
var _deleteCallback = null;
function showDeleteConfirm(title, msg, callback) {
    document.getElementById('del-modal-title').textContent = title;
    document.getElementById('del-modal-msg').textContent   = msg;
    var btn = document.getElementById('del-modal-btn');
    btn.disabled = false;
    document.getElementById('del-modal-btn-text').textContent = 'Hapus';
    _deleteCallback = callback;
    openModal('m-delete-confirm');
}
function closeDeleteConfirm() {
    closeModal('m-delete-confirm');
    _deleteCallback = null;
}
function confirmDelete() {
    if (!_deleteCallback) return;
    var btn = document.getElementById('del-modal-btn');
    btn.disabled = true;
    document.getElementById('del-modal-btn-text').textContent = 'Menghapus...';
    _deleteCallback(function() { closeDeleteConfirm(); });
}

function toggleSidebar() {
    const sb = document.querySelector('.sb');
    const ov = document.getElementById('sb-overlay');
    sb.classList.toggle('open');
    ov.classList.toggle('active');
    document.body.style.overflow = sb.classList.contains('open') ? 'hidden' : '';
}
</script>
@stack('scripts')
</body>
</html>
