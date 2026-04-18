<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gatekeeper @yield('title', 'Dashboard')</title>
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
            background: #1e40af;
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
        .ni:hover { background: #f1f5f9; color: #0f172a; }
        .ni.active { background: #eff6ff; color: #1d4ed8; font-weight: 600; }
        .ni svg { width: 15px; height: 15px; flex-shrink: 0; }
        .ni .bdg {
            margin-left: auto; font-size: 10px;
            padding: 1px 7px; border-radius: 20px;
            background: #dbeafe; color: #1e3a8a; font-weight: 700;
        }
        .sb-foot {
            padding: 12px 14px;
            border-top: 1px solid #e2e8f0;
            display: flex; align-items: center; gap: 9px;
        }
        .av {
            width: 30px; height: 30px; border-radius: 50%;
            background: #eff6ff;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 700; color: #1d4ed8; flex-shrink: 0;
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
        .tb-sub { font-size: 12px; color: #64748b; margin-top: 2px; }
        .content { padding: 24px; }

        /* ── Stats grid ───────────────────────────────── */
        .sg {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px; margin-bottom: 24px;
        }
        .sc {
            background: #fff; border-radius: 10px;
            border: 1px solid #e2e8f0; padding: 16px 18px;
        }
        .sc-ico {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 10px;
        }
        .sc-lbl { font-size: 12px; color: #64748b; margin-bottom: 4px; }
        .sc-val { font-size: 26px; font-weight: 700; color: #0f172a; }
        .sc-sub { font-size: 11px; color: #94a3b8; margin-top: 3px; }

        /* ── Section / Table card ─────────────────────── */
        .g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .sh {
            display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 12px;
        }
        .st { font-size: 14px; font-weight: 700; color: #0f172a; }
        .tc {
            background: #fff; border-radius: 10px;
            border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 20px;
        }
        .tbar {
            display: flex; align-items: center; gap: 9px;
            padding: 13px 16px; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap;
        }
        .sw { position: relative; flex: 1; min-width: 140px; max-width: 240px; }
        .sw svg {
            position: absolute; left: 9px; top: 50%; transform: translateY(-50%);
            width: 13px; height: 13px; color: #94a3b8;
        }
        .sw input {
            width: 100%; padding: 7px 9px 7px 28px;
            font-size: 13px; border: 1px solid #cbd5e1;
            border-radius: 7px; outline: none; color: #0f172a;
        }
        .sw input:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px #eff6ff; }
        select.fsel {
            padding: 7px 9px; font-size: 12px;
            border: 1px solid #cbd5e1; border-radius: 7px;
            outline: none; color: #374151; background: #fff;
        }
        .rc { margin-left: auto; font-size: 12px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        thead th {
            padding: 9px 14px; text-align: left;
            font-size: 11px; font-weight: 700; color: #64748b;
            background: #f8fafc; border-bottom: 1px solid #e2e8f0;
            text-transform: uppercase; letter-spacing: .05em;
        }
        tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f8fafc; }
        tbody td { padding: 10px 14px; font-size: 13px; color: #0f172a; }
        .mono { font-size: 11px; font-family: 'Courier New', monospace; color: #64748b; }
        .trunc { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 12px; color: #64748b; }
        .ini {
            width: 28px; height: 28px; border-radius: 50%;
            background: #eff6ff;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 700; color: #1d4ed8; flex-shrink: 0;
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
        .bico.del:hover { background: #fef2f2; border-color: #fecaca; }
        .bico.del:hover svg { color: #dc2626; }
        .pag {
            padding: 11px 16px; border-top: 1px solid #e2e8f0;
            font-size: 12px; color: #64748b;
        }
        .empty { text-align: center; padding: 40px; color: #94a3b8; font-size: 13px; }

        /* ── Buttons ──────────────────────────────────── */
        .btn-sm {
            padding: 7px 13px; border-radius: 7px;
            border: 1px solid #cbd5e1; font-size: 12px;
            color: #374151; cursor: pointer; background: #fff;
        }
        .btn-sm:hover { background: #f1f5f9; }
        .btn-add {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 7px 14px; border-radius: 7px; border: none;
            font-size: 12px; font-weight: 600; cursor: pointer;
        }
        .btn-add:hover { opacity: .88; }
        .b-blue { background: #bfdbfe; color: #1e3a8a; }
        .b-teal { background: #99f6e4; color: #134e4a; }

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
        .mttl { font-size: 15px; font-weight: 700; margin-bottom: 18px; color: #0f172a; }
        .fg { margin-bottom: 13px; }
        .fl { font-size: 12px; color: #475569; margin-bottom: 5px; display: block; font-weight: 500; }
        .fi {
            width: 100%; padding: 8px 11px; font-size: 13px;
            border: 1px solid #cbd5e1; border-radius: 7px; outline: none; color: #0f172a;
        }
        .fi:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px #eff6ff; }
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

        /* ── Alert flash ─────────────────────────────── */
        .alert {
            padding: 11px 16px; border-radius: 8px;
            font-size: 13px; margin-bottom: 16px;
            display: flex; align-items: center; gap: 8px;
        }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══════════════════ SIDEBAR ═══════════════════ --}}
<div class="sb">
    <div class="sb-logo">
        <div class="sb-ico">
            <svg viewBox="0 0 14 14" fill="none">
                <path d="M7 1L1.5 3.5v4C1.5 10.5 4 13 7 13.5 10 13 12.5 10.5 12.5 7.5v-4L7 1z"
                      stroke="white" stroke-width="1.4" stroke-linejoin="round"/>
            </svg>
        </div>
        <div>
            <div class="sb-name">Gatekeeper</div>
            <div class="sb-role">Panel manajemen siaran</div>
        </div>
    </div>

    <div class="sb-nav">
        <div class="nav-lbl">Menu</div>
        <a href="{{ route('gatekeeper.dashboard') }}"
           class="ni {{ request()->routeIs('gatekeeper.dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 15 15" fill="none">
                <rect x="1" y="1" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3"/>
                <rect x="8.5" y="1" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3"/>
                <rect x="1" y="8.5" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3"/>
                <rect x="8.5" y="8.5" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3"/>
            </svg>
            Dashboard
        </a>

        <div class="nav-lbl">Fitur</div>
        <a href="{{ route('gatekeeper.dashboard') }}#siaran"
           class="ni {{ request()->routeIs('gatekeeper.dashboard') && request()->get('tab') === 'siaran' ? 'active' : '' }}"
           onclick="showTab('siaran');return false;">
            <svg viewBox="0 0 15 15" fill="none">
                <circle cx="7.5" cy="7.5" r="2.8" stroke="currentColor" stroke-width="1.3"/>
                <circle cx="7.5" cy="7.5" r="6" stroke="currentColor" stroke-width="1.3"/>
                <path d="M2 7.5h2M11 7.5h2M7.5 2v2M7.5 11v2"
                      stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            Informasi Siaran
            <span class="bdg" id="bdg-s">{{ $stats['total_siaran'] }}</span>
        </a>
        <a href="{{ route('gatekeeper.dashboard') }}#narasumber"
           class="ni {{ request()->routeIs('gatekeeper.dashboard') && request()->get('tab') === 'narasumber' ? 'active' : '' }}"
           onclick="showTab('narasumber');return false;">
            <svg viewBox="0 0 15 15" fill="none">
                <circle cx="7.5" cy="4.5" r="2.5" stroke="currentColor" stroke-width="1.3"/>
                <path d="M2 13c0-3 2.5-5 5.5-5s5.5 2 5.5 5"
                      stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            Narasumber Byphone
            <span class="bdg" id="bdg-n">{{ $stats['total_narasumber'] }}</span>
        </a>
    </div>

    <div class="sb-foot">
        <div class="av">{{ strtoupper(substr(auth()->user()->name ?? 'GK', 0, 2)) }}</div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:12px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                {{ auth()->user()->name ?? 'Gatekeeper' }}
            </div>
            <div style="font-size:10px;color:#94a3b8">Gatekeeper</div>
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

{{-- ═══════════════════ MAIN ═══════════════════ --}}
<div class="main">
    <div class="topbar">
        <div>
            <div class="tb-title" id="pg-title">@yield('page-title', 'Dashboard')</div>
            <div class="tb-sub" id="pg-sub">@yield('page-sub', 'Ringkasan data siaran dan narasumber')</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="font-size:12px;color:#64748b;" id="clk"></div>
            <div style="position:relative;" id="tb-profile-wrap">
                <div class="av" style="cursor:pointer;" onclick="toggleTbMenu()" title="Menu Profil">
                    {{ strtoupper(substr(auth()->user()->name ?? 'GK', 0, 2)) }}
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
            <div class="alert alert-success">
                <svg width="14" height="14" viewBox="0 0 15 15" fill="none">
                    <path d="M12.5 4.5l-7 7-3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <svg width="14" height="14" viewBox="0 0 15 15" fill="none">
                    <path d="M7.5 1v7M7.5 11v1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
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
    return name.split(' ')
        .filter(w => /[A-Za-z]/.test(w))
        .slice(0, 2)
        .map(w => w[0].toUpperCase())
        .join('');
}

// CSRF helper untuk fetch
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
function apiFetch(url, method = 'GET', body = null) {
    const opts = {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    };
    if (body) opts.body = JSON.stringify(body);
    return fetch(url, opts).then(r => r.json());
}

function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}

// Close modal when clicking backdrop
document.querySelectorAll('.mbg').forEach(bg => {
    bg.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});
</script>
@stack('scripts')
</body>
</html>