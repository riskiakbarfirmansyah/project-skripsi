<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Penyiar @yield('title', 'Dashboard')</title>
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
        }
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

        /* ── Alert ────────────────────────────────────── */
        .alert {
            padding: 11px 16px; border-radius: 8px;
            font-size: 13px; margin-bottom: 16px;
            display: flex; align-items: center; gap: 8px;
        }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        @media (max-width: 768px) {
            .sb { transform: translateX(-224px); transition: transform .2s; }
            .main { margin-left: 0; }
            .sg { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══════════════════ SIDEBAR ═══════════════════ --}}
<div class="sb">
    <div class="sb-logo">
        <div class="sb-ico">
            <svg viewBox="0 0 14 14" fill="none">
                <circle cx="7" cy="7" r="2.8" stroke="white" stroke-width="1.4"/>
                <circle cx="7" cy="7" r="5.5" stroke="white" stroke-width="1.4"/>
                <path d="M2 7h1.5M10.5 7H12M7 2v1.5M7 10.5V12" stroke="white" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
        </div>
        <div>
            <div class="sb-name">Penyiar</div>
            <div class="sb-role">Panel data narasumber</div>
        </div>
    </div>

    <div class="sb-nav">
        <div class="nav-lbl">Menu</div>
        <a href="{{ route('penyiar.dashboard') }}"
           class="ni {{ request()->routeIs('penyiar.dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 15 15" fill="none">
                <rect x="1" y="1" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3"/>
                <rect x="8.5" y="1" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3"/>
                <rect x="1" y="8.5" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3"/>
                <rect x="8.5" y="8.5" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3"/>
            </svg>
            Dashboard
        </a>

        <div class="nav-lbl">Fitur</div>
        <a href="{{ route('penyiar.dashboard') }}#byphone"
           class="ni" onclick="showTab('byphone');return false;">
            <svg viewBox="0 0 15 15" fill="none">
                <path d="M5 1.5C4.5 1.5 4 2 4 2.5L3 5c-.5 1 0 2 1 2.5 1.5 1 3.5 3 4.5 4.5.5 1 1.5 1.5 2.5 1L13 12c.5-.5.5-1.5 0-2L11 8.5c-.5-.5-1.5-.5-2 0l-.5.5C8 8.5 7 7.5 6.5 7l.5-.5c.5-.5.5-1.5 0-2L5.5 2c-.5-.5-1-.5-0.5-.5z"
                      stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
            </svg>
            Narasumber Byphone
            <span class="bdg">{{ $stats['total_byphone'] ?? 0 }}</span>
        </a>
        <a href="{{ route('penyiar.dashboard') }}#talkshow"
           class="ni" onclick="showTab('talkshow');return false;">
            <svg viewBox="0 0 15 15" fill="none">
                <rect x="1.5" y="1.5" width="12" height="12" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
                <path d="M4.5 8l2 2 4-4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Narasumber Talkshow
            <span class="bdg">{{ $stats['total_talkshow'] ?? 0 }}</span>
        </a>
    </div>

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

{{-- ═══════════════════ MAIN ═══════════════════ --}}
<div class="main">
    <div class="topbar">
        <div>
            <div class="tb-title" id="pg-title">@yield('page-title', 'Dashboard')</div>
            <div class="tb-sub" id="pg-sub">@yield('page-sub', 'Data narasumber byphone & talkshow')</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="font-size:12px;color:#64748b;" id="clk"></div>
            <div class="av">{{ strtoupper(substr(auth()->user()->name ?? 'PY', 0, 2)) }}</div>
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
        <div id="flash-msg" style="display:none;"></div>

        @yield('content')
    </div>
</div>

<script>
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
    el.className = 'alert alert-' + type;
    el.textContent = msg;
    el.style.display = 'flex';
    setTimeout(() => { el.style.display = 'none'; }, 4000);
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
        if (e.target === this) closeModal(this.id);
    });
});
</script>
@stack('scripts')
</body>
</html>
