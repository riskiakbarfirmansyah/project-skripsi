@extends('layouts.penyiar')

@section('title', 'Dashboard')
@section('page-title', 'Siaran narasumber byphone & talkshow')

@push('styles')
<style>
.wa-link { display:inline-flex; align-items:center; gap:4px; color:#16a34a; text-decoration:none; font-weight:500; transition:color .15s; }
.wa-link:hover { color:#15803d; text-decoration:underline; }

/* ── Schedule badge (read-only) ── */
.sched-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; border-radius: 12px; font-size: 11px;
    font-weight: 600; background: #ccfbf1; color: #065f46;
}
.sched-none { color:#94a3b8; font-size:11px; }

/* ── Status Siaran badges ── */
.bdg-siaran-belum   { display:inline-flex;align-items:center;gap:4px;padding:2px 9px;border-radius:12px;font-size:11px;font-weight:600;background:#f1f5f9;color:#475569;white-space:nowrap; }
.bdg-siaran-aktif   { display:inline-flex;align-items:center;gap:4px;padding:2px 9px;border-radius:12px;font-size:11px;font-weight:600;background:#fef2f2;color:#dc2626;white-space:nowrap; }
.bdg-siaran-selesai { display:inline-flex;align-items:center;gap:4px;padding:2px 9px;border-radius:12px;font-size:11px;font-weight:600;background:#d1fae5;color:#065f46;white-space:nowrap; }
.bdg-siaran-dot { width:6px;height:6px;border-radius:50%;background:#dc2626;flex-shrink:0;animation:bdg-pulse 1.4s infinite; }
@keyframes bdg-pulse {
    0%   { box-shadow:0 0 0 0 rgba(239,68,68,.4); }
    70%  { box-shadow:0 0 0 5px rgba(239,68,68,0); }
    100% { box-shadow:0 0 0 0 rgba(239,68,68,0); }
}

/* ── Broadcast card ── */
.bc-card {
    background:#fff; border-radius:10px; border:1px solid #e2e8f0;
    padding:20px 24px; margin-bottom:24px;
    display:flex; align-items:center; justify-content:space-between; gap:16px;
}
.bc-card.on-air {
    border-color:#059669; background:#f0fdf4;
}
.bc-status-dot {
    width:10px; height:10px; border-radius:50%; background:#94a3b8; flex-shrink:0;
}
.bc-status-dot.live {
    background:#ef4444;
    box-shadow:0 0 0 0 rgba(239,68,68,.4);
    animation:pulse-dot 1.4s infinite;
}
@keyframes pulse-dot {
    0%   { box-shadow:0 0 0 0 rgba(239,68,68,.4); }
    70%  { box-shadow:0 0 0 8px rgba(239,68,68,0); }
    100% { box-shadow:0 0 0 0 rgba(239,68,68,0); }
}
.bc-info { display:flex; align-items:center; gap:12px; flex:1; min-width:0; }
.bc-label { font-size:13px; font-weight:700; color:#0f172a; }
.bc-sub   { font-size:12px; color:#64748b; margin-top:2px; }
.bc-timer { font-size:20px; font-weight:700; color:#059669; letter-spacing:.04em; font-family:'Courier New',monospace; }
.btn-siap {
    padding:12px 28px; font-size:14px; font-weight:600;
    border-radius:10px; border:none; cursor:pointer; color:#fff;
    background:linear-gradient(135deg,#059669,#047857);
    transition:all .2s ease;
    display:inline-flex; align-items:center; gap:8px;
    box-shadow:0 2px 8px rgba(5,150,105,.15);
}
.btn-siap:hover {
    transform:translateY(-2px);
    box-shadow:0 6px 16px rgba(5,150,105,.3);
}
.btn-siap:active { transform:translateY(0); }

.btn-selesai {
    padding:12px 28px; font-size:14px; font-weight:600;
    border-radius:10px; border:none; cursor:pointer; color:#fff;
    background:linear-gradient(135deg,#dc2626,#b91c1c);
    transition:all .2s ease;
    display:inline-flex; align-items:center; gap:8px;
    box-shadow:0 2px 8px rgba(220,38,38,.15);
    animation:pulse-red 2s ease-in-out infinite;
}
.btn-selesai:hover {
    transform:translateY(-2px);
    box-shadow:0 6px 20px rgba(220,38,38,.4);
    animation:none;
}
.btn-selesai:active { transform:translateY(0); }

@keyframes pulse-red {
    0%,100% { box-shadow:0 2px 8px rgba(220,38,38,.15); }
    50%      { box-shadow:0 2px 16px rgba(220,38,38,.35); }
}

/* ── Live broadcast detail block ── */
.bc-detail {
    background:#fff; border-radius:10px; border:2px solid #059669;
    padding:20px 24px; margin-bottom:24px; display:none;
}
.bc-detail.visible { display:block; }
.bc-detail-head {
    display:flex; align-items:center; gap:10px; margin-bottom:16px;
}
.live-badge {
    display:inline-flex; align-items:center; gap:6px;
    background:#fef2f2; color:#dc2626;
    font-size:11px; font-weight:700; padding:3px 10px;
    border-radius:20px; letter-spacing:.04em;
}
.bc-narasumber-grid {
    display:grid; grid-template-columns:repeat(3,1fr); gap:10px;
    margin-bottom:16px;
}
.bc-ng-item {
    background:#f8fafc; border-radius:8px; padding:10px 14px;
}
.bc-ng-lbl { font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin-bottom:4px; }
.bc-ng-val { font-size:13px; color:#0f172a; font-weight:600; }
.bc-draft-box {
    background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px;
    padding:14px 16px; font-size:13px; color:#374151;
    line-height:1.7; white-space:pre-wrap; max-height:260px; overflow-y:auto;
}
.bc-draft-title {
    font-size:12px; font-weight:700; color:#065f46;
    text-transform:uppercase; letter-spacing:.06em; margin-bottom:8px;
}

/* ── Custom date filter ── */
.date-filter-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    background: #fff;
    cursor: pointer;
    font-size: 12px;
    color: #374151;
    white-space: nowrap;
    transition: border-color .15s, background .15s;
    min-width: 148px;
    user-select: none;
}
.date-filter-wrap:hover { border-color: #94a3b8; }
.date-filter-wrap.has-value { border-color: #6366f1; background: #f5f3ff; color: #4338ca; font-weight: 600; }
.date-filter-wrap input[type="date"] {
    position: absolute; opacity: 0; width: 1px; height: 1px; pointer-events: none;
}
.date-clear-btn {
    background: none; border: none; cursor: pointer;
    color: #94a3b8; font-size: 15px; line-height: 1;
    padding: 0 0 0 4px; margin-left: auto;
    transition: color .15s;
}
.date-clear-btn:hover { color: #ef4444; }

/* ── Notes modal ── */
.notes-ta {
    width:100%; padding:9px 12px; font-size:13px;
    border:1px solid #cbd5e1; border-radius:7px; outline:none;
    resize:vertical; min-height:80px; color:#0f172a;
}
.notes-ta:focus { border-color:#6ee7b7; box-shadow:0 0 0 3px #ecfdf5; }
.done-check {
    width:68px; height:68px; border-radius:50%;
    background:linear-gradient(135deg,#d1fae5,#a7f3d0);
    display:flex; align-items:center; justify-content:center; margin:0 auto 16px;
    box-shadow:0 4px 16px rgba(5,150,105,.2);
}
</style>
@endpush

@section('sidebar-stats')
<div class="sb-stats">
    <div class="sb-stats-title">Ringkasan</div>
    <div class="sb-stats-grid">
        <div class="sb-sc">
            <div class="sb-sc-ico" style="background:#d1fae5;"><svg viewBox="0 0 15 15" fill="none"><path d="M5 1.5C4.5 1.5 4 2 4 2.5L3 5c-.5 1 0 2 1 2.5 1.5 1 3.5 3 4.5 4.5.5 1 1.5 1.5 2.5 1L13 12c.5-.5.5-1.5 0-2L11 8.5c-.5-.5-1.5-.5-2 0l-.5.5C8 8.5 7 7.5 6.5 7l.5-.5c.5-.5.5-1.5 0-2L5.5 2c-.5-.5-1-.5-0.5-.5z" stroke="#065f46" stroke-width="1.3" stroke-linejoin="round"/></svg></div>
            <div class="sb-sc-val">{{ $stats['total_byphone'] }}</div>
            <div class="sb-sc-lbl">Byphone</div>
        </div>
        <div class="sb-sc">
            <div class="sb-sc-ico" style="background:#dbeafe;"><svg viewBox="0 0 15 15" fill="none"><rect x="1.5" y="1.5" width="12" height="12" rx="1.5" stroke="#1e40af" stroke-width="1.3"/><path d="M4.5 8l2 2 4-4" stroke="#1e40af" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            <div class="sb-sc-val">{{ $stats['total_talkshow'] }}</div>
            <div class="sb-sc-lbl">Talkshow</div>
        </div>
        <div class="sb-sc">
            <div class="sb-sc-ico" style="background:#fef3c7;"><svg viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="5.5" stroke="#92400e" stroke-width="1.3"/><path d="M7.5 4.5v3l2 2" stroke="#92400e" stroke-width="1.3" stroke-linecap="round"/></svg></div>
            <div class="sb-sc-val">{{ $stats['byphone_hari_ini'] }}</div>
            <div class="sb-sc-lbl">Byphone Hari Ini</div>
        </div>
        <div class="sb-sc">
            <div class="sb-sc-ico" style="background:#fce7f3;"><svg viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="5.5" stroke="#9d174d" stroke-width="1.3"/><path d="M7.5 4.5v3l2 2" stroke="#9d174d" stroke-width="1.3" stroke-linecap="round"/></svg></div>
            <div class="sb-sc-val">{{ $stats['talkshow_hari_ini'] }}</div>
            <div class="sb-sc-lbl">Talkshow Hari Ini</div>
        </div>
    </div>
</div>
@endsection

@section('content')

{{-- ════════════════════ BROADCAST STATUS CARD ════════════════════ --}}
@php $isOnAir = isset($activeBroadcast) && $activeBroadcast !== null; @endphp

<div class="bc-card {{ $isOnAir ? 'on-air' : '' }}" id="bc-card">
    <div class="bc-info">
        <div class="bc-status-dot {{ $isOnAir ? 'live' : '' }}" id="bc-dot"></div>
        <div>
            <div class="bc-label" id="bc-label">{{ $isOnAir ? 'Sedang Siaran' : 'Siap Siaran' }}</div>
            <div class="bc-sub" id="bc-sub">
                @if($isOnAir)
                    {{ $activeBroadcast->narasumber_detail?->nama ?? '—' }} &middot;
                    {{ $activeBroadcast->narasumber_detail?->tema_wawancara ?? '—' }}
                @else
                    Tekan tombol untuk memulai siaran
                @endif
            </div>
        </div>
    </div>
    @if($isOnAir)
        <div class="bc-timer" id="bc-timer">00:00:00</div>
        <button class="btn-selesai" onclick="endBroadcast()">
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                <rect x="3" y="3" width="9" height="9" rx="1.5" fill="currentColor"/>
            </svg>
            Siaran Selesai
        </button>
    @else
        <button class="btn-siap" id="btn-siap" onclick="openStartModal()">
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                <path d="M7.5 1.5a2.5 2.5 0 0 1 2.5 2.5v4a2.5 2.5 0 0 1-5 0V4a2.5 2.5 0 0 1 2.5-2.5z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
                <path d="M3 7.5A4.5 4.5 0 0 0 12 7.5M7.5 12v2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            Siap Siaran
        </button>
    @endif
</div>

{{-- ── Live detail block ── --}}
<div class="bc-detail {{ $isOnAir ? 'visible' : '' }}" id="bc-detail">
    <div class="bc-detail-head">
        <span class="live-badge">
            <span style="width:7px;height:7px;border-radius:50%;background:#dc2626;display:inline-block;"></span>
            ON AIR
        </span>
        <span style="font-size:14px;font-weight:700;color:#0f172a;">Informasi Narasumber</span>
    </div>
    <div class="bc-narasumber-grid" id="bc-nar-grid">
        <div class="bc-ng-item">
            <div class="bc-ng-lbl">Nama</div>
            <div class="bc-ng-val" id="bc-nar-nama">{{ $isOnAir ? ($activeBroadcast->narasumber_detail?->nama ?? '—') : '—' }}</div>
        </div>
        <div class="bc-ng-item">
            <div class="bc-ng-lbl">Instansi</div>
            <div class="bc-ng-val" id="bc-nar-instansi">{{ $isOnAir ? ($activeBroadcast->narasumber_detail?->instansi ?? '—') : '—' }}</div>
        </div>
        <div class="bc-ng-item">
            <div class="bc-ng-lbl">Tema Wawancara</div>
            <div class="bc-ng-val" id="bc-nar-tema">{{ $isOnAir ? ($activeBroadcast->narasumber_detail?->tema_wawancara ?? '—') : '—' }}</div>
        </div>
    </div>
    <div class="bc-draft-title">Draft Pertanyaan</div>
    <div class="bc-draft-box" id="bc-draft">{{ $isOnAir ? ($activeBroadcast->narasumber_detail?->draft_pertanyaan ?? '(Tidak ada draft pertanyaan)') : '—' }}</div>
</div>

{{-- ── Tabs ── --}}
<div class="tabs">
    <button class="tab-btn active" id="tab-byphone-btn" onclick="showTab('byphone')">
        Narasumber Byphone
    </button>
    <button class="tab-btn" id="tab-talkshow-btn" onclick="showTab('talkshow')">
        Narasumber Talkshow
    </button>
</div>

{{-- ════════════════════ TAB: BYPHONE ════════════════════ --}}
<div class="tab-pane active" id="tab-byphone">
    <div class="tc">
        <div class="tbar">
            <div class="sw">
                <svg viewBox="0 0 15 15" fill="none">
                    <circle cx="6.5" cy="6.5" r="4" stroke="currentColor" stroke-width="1.3"/>
                    <path d="M11 11l2.5 2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                </svg>
                <input type="text" id="bp-search" placeholder="Cari nama, instansi, tema, jadwal…" oninput="debounce(doSearchBp, 350)()">
            </div>
            <span style="font-size:12px;color:#64748b;white-space:nowrap;">Filter tanggal:</span>
            <div class="date-filter-wrap" id="bp-tanggal-wrap" onclick="triggerDatePicker('bp-tanggal')">
                <svg width="13" height="13" viewBox="0 0 15 15" fill="none"><rect x="1.5" y="2.5" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M10 1v3M1.5 6h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                <span id="bp-tanggal-lbl">Semua tanggal</span>
                <button type="button" class="date-clear-btn" style="display:none" onclick="event.stopPropagation();clearDateFilter('bp-tanggal','bp-tanggal-lbl','bp-tanggal-wrap',doSearchBp)">×</button>
                <input type="date" id="bp-tanggal" onchange="syncDateDisplay('bp-tanggal-lbl','bp-tanggal-wrap',this.value);doSearchBp()">
            </div>
            <select class="fsel" id="bp-limit" onchange="doSearchBp()">
                <option value="10">10</option>
                <option value="15" selected>15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="rc" id="bp-count">{{ $byphoneList->count() }} data</span>
        </div>

        <div style="font-weight:600;color:#64748b;font-size:13px;padding:0 8px 10px;">
            Tabel Narasumber Byphone
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width:60px">Nomor</th>
                        <th style="width:140px">Nama</th>
                        <th style="width:140px">Instansi</th>
                        <th style="width:120px">No. Telepon</th>
                        <th style="width:200px">Tema Wawancara</th>
                        <th style="width:115px">Waktu</th>
                        <th style="width:150px">Jenis Jadwal</th>
                        <th style="width:130px">Status Siaran</th>
                        <th style="width:180px">Aksi</th>
                    </tr>
                </thead>
                <tbody id="bp-tbody">
                    @forelse($byphoneList as $i => $n)
                    <tr>
                        <td class="mono">{{ $byphoneList->firstItem() + $i }}</td>
                        <td>
                            <div style="font-weight:600;font-size:13px;">{{ $n->nama }}</div>
                        </td>
                        <td style="font-size:12px;color:#64748b;">{{ $n->instansi }}</td>
                        <td class="mono"><a href="{{ wa_link($n->nomor_telp) }}" target="_blank" rel="noopener" class="wa-link"><svg width="13" height="13" viewBox="0 0 24 24" fill="#25d366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>{{ $n->nomor_telp }}</a></td>
                        <td>{{ $n->tema_wawancara }}</td>
                        <td class="mono">{{ format_tanggal_id($n->waktu_wawancara) }} {{ \Carbon\Carbon::parse($n->waktu_wawancara)->format('H:i') }}</td>
                        <td>
                            @if($n->byphone_schedule)
                                <span class="sched-badge">{{ \App\Services\JadwalValidationService::SCHEDULES[$n->byphone_schedule] ?? $n->byphone_schedule }}</span>
                            @else
                                <span class="sched-none">—</span>
                            @endif
                        </td>
                        <td>
                            @php $bs = $n->broadcast_status ?? 'belum_siaran'; @endphp
                            @if($bs === 'sedang_siaran')
                                <span class="bdg-siaran-aktif"><span class="bdg-siaran-dot"></span>Sedang Siaran</span>
                            @elseif($bs === 'sudah_siaran')
                                <span class="bdg-siaran-selesai">✅ Sudah Siaran</span>
                            @else
                                <span class="bdg-siaran-belum">⏳ Belum Siaran</span>
                            @endif
                        </td>
                        <td>
                            <button class="bico" title="Detail" onclick="viewBp({{ $n->byphoneID }})">
                                <svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="empty">Belum ada data narasumber byphone.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pag" id="bp-pag"></div>
    </div>
</div>

{{-- ════════════════════ TAB: TALKSHOW ════════════════════ --}}
<div class="tab-pane" id="tab-talkshow">
    <div class="tc">
        <div class="tbar">
            <div class="sw">
                <svg viewBox="0 0 15 15" fill="none">
                    <circle cx="6.5" cy="6.5" r="4" stroke="currentColor" stroke-width="1.3"/>
                    <path d="M11 11l2.5 2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                </svg>
                <input type="text" id="ts-search" placeholder="Cari nama, instansi, tema, jadwal…" oninput="debounce(doSearchTs, 350)()">
            </div>
            <span style="font-size:12px;color:#64748b;white-space:nowrap;">Filter tanggal:</span>
            <div class="date-filter-wrap" id="ts-tanggal-wrap" onclick="triggerDatePicker('ts-tanggal')">
                <svg width="13" height="13" viewBox="0 0 15 15" fill="none"><rect x="1.5" y="2.5" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M10 1v3M1.5 6h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                <span id="ts-tanggal-lbl">Semua tanggal</span>
                <button type="button" class="date-clear-btn" style="display:none" onclick="event.stopPropagation();clearDateFilter('ts-tanggal','ts-tanggal-lbl','ts-tanggal-wrap',doSearchTs)">×</button>
                <input type="date" id="ts-tanggal" onchange="syncDateDisplay('ts-tanggal-lbl','ts-tanggal-wrap',this.value);doSearchTs()">
            </div>
            <select class="fsel" id="ts-limit" onchange="doSearchTs()">
                <option value="10">10</option>
                <option value="15" selected>15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="rc" id="ts-count">{{ $talkshowList->count() }} data</span>
        </div>

        <div style="font-weight:600;color:#64748b;font-size:13px;padding:0 8px 10px;">
            Tabel Narasumber Talkshow
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width:60px">Nomor</th>
                        <th style="width:140px">Nama</th>
                        <th style="width:140px">Instansi</th>
                        <th style="width:120px">No. Telepon</th>
                        <th style="width:200px">Tema Wawancara</th>
                        <th style="width:115px">Waktu</th>
                        <th style="width:150px">Jenis Jadwal</th>
                        <th style="width:130px">Status Siaran</th>
                        <th style="width:180px">Aksi</th>
                    </tr>
                </thead>
                <tbody id="ts-tbody">
                    @forelse($talkshowList as $i => $n)
                    <tr>
                        <td class="mono">{{ $talkshowList->firstItem() + $i }}</td>
                        <td>
                            <div style="font-weight:600;font-size:13px;">{{ $n->nama }}</div>
                        </td>
                        <td style="font-size:12px;color:#64748b;">{{ $n->instansi }}</td>
                        <td class="mono"><a href="{{ wa_link($n->nomor_telp) }}" target="_blank" rel="noopener" class="wa-link"><svg width="13" height="13" viewBox="0 0 24 24" fill="#25d366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>{{ $n->nomor_telp }}</a></td>
                        <td>{{ $n->tema_wawancara }}</td>
                        <td class="mono">{{ format_tanggal_id($n->waktu_wawancara) }} {{ \Carbon\Carbon::parse($n->waktu_wawancara)->format('H:i') }}</td>
                        <td>
                            @if($n->talkshow_schedule)
                                <span class="sched-badge">{{ \App\Services\JadwalValidationService::SCHEDULES[$n->talkshow_schedule] ?? $n->talkshow_schedule }}</span>
                            @else
                                <span class="sched-none">—</span>
                            @endif
                        </td>
                        <td>
                            @php $bs = $n->broadcast_status ?? 'belum_siaran'; @endphp
                            @if($bs === 'sedang_siaran')
                                <span class="bdg-siaran-aktif"><span class="bdg-siaran-dot"></span>Sedang Siaran</span>
                            @elseif($bs === 'sudah_siaran')
                                <span class="bdg-siaran-selesai">✅ Sudah Siaran</span>
                            @else
                                <span class="bdg-siaran-belum">⏳ Belum Siaran</span>
                            @endif
                        </td>
                        <td>
                            <button class="bico" title="Detail" onclick="viewTs({{ $n->talkshowID }})">
                                <svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="empty">Belum ada data narasumber talkshow.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pag" id="ts-pag"></div>
    </div>
</div>

{{-- ════════════════════ MODAL DETAIL BYPHONE ════════════════════ --}}
<div class="mbg" id="m-bp-view">
    <div class="modal">
        <div class="mttl">
            Detail Narasumber Byphone
            <button class="mclose" onclick="closeModal('m-bp-view')">×</button>
        </div>
        <div class="dr"><span class="dl">Nama</span><span class="dv" id="bv-nama">—</span></div>
        <div class="dr"><span class="dl">Instansi</span><span class="dv" id="bv-instansi">—</span></div>
        <div class="dr"><span class="dl">Nomor Telepon</span><span class="dv" id="bv-nomor">—</span></div>
        <div class="dr"><span class="dl">Tema Wawancara</span><span class="dv" id="bv-tema">—</span></div>
        <div class="dr"><span class="dl">Waktu Wawancara</span><span class="dv" id="bv-waktu">—</span></div>
        <div class="dr" style="flex-direction:column;">
            <span class="dl">Draft Pertanyaan</span>
            <div class="dbox" id="bv-draft">—</div>
        </div>
        <div class="mft"><button class="bcn" onclick="closeModal('m-bp-view')">Tutup</button></div>
    </div>
</div>

{{-- ════════════════════ MODAL DETAIL TALKSHOW ════════════════════ --}}
<div class="mbg" id="m-ts-view">
    <div class="modal">
        <div class="mttl">
            Detail Narasumber Talkshow
            <button class="mclose" onclick="closeModal('m-ts-view')">×</button>
        </div>
        <div class="dr"><span class="dl">Nama</span><span class="dv" id="tv-nama">—</span></div>
        <div class="dr"><span class="dl">Instansi</span><span class="dv" id="tv-instansi">—</span></div>
        <div class="dr"><span class="dl">Nomor Telepon</span><span class="dv" id="tv-nomor">—</span></div>
        <div class="dr"><span class="dl">Tema Wawancara</span><span class="dv" id="tv-tema">—</span></div>
        <div class="dr"><span class="dl">Waktu Wawancara</span><span class="dv" id="tv-waktu">—</span></div>
        <div class="dr"><span class="dl">Jadwal Talkshow</span><span class="dv" id="tv-jadwal">—</span></div>
        <div class="dr" style="flex-direction:column;">
            <span class="dl">Draft Pertanyaan</span>
            <div class="dbox" id="tv-draft">—</div>
        </div>
        <div class="mft"><button class="bcn" onclick="closeModal('m-ts-view')">Tutup</button></div>
    </div>
</div>

{{-- ════════════════════ MODAL MULAI SIARAN ════════════════════ --}}
<div class="mbg" id="m-bc-start">
    <div class="modal">
        <div class="mttl">
            Siap Siaran
            <button class="mclose" onclick="closeModal('m-bc-start')">×</button>
        </div>
        <div class="fg">
            <label class="fl">Jenis Narasumber</label>
            <select class="fi" id="bc-type" onchange="loadNarasumberOptions()">
                <option value="byphone">Narasumber Byphone</option>
                <option value="talkshow">Narasumber Talkshow</option>
            </select>
        </div>
        <div class="fg">
            <label class="fl">Pilih Narasumber</label>
            <select class="fi" id="bc-narasumber">
                <option value="">— Pilih narasumber —</option>
            </select>
            <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                Menampilkan narasumber hari ini. ✅ = sudah siaran, 🔴 = sedang siaran.
            </div>
        </div>
        <div id="bc-preview" style="display:none;background:#f8fafc;border-radius:8px;padding:12px 14px;margin-bottom:13px;font-size:13px;">
            <div style="font-weight:600;color:#0f172a;margin-bottom:4px;" id="bc-prev-nama">—</div>
            <div style="color:#64748b;font-size:12px;" id="bc-prev-tema">—</div>
        </div>
        <div class="mft">
            <button class="bcn" onclick="closeModal('m-bc-start')">Batal</button>
            <button class="bsv" style="padding:10px 24px;border-radius:10px;font-size:14px;background:linear-gradient(135deg,#059669,#047857);box-shadow:0 2px 8px rgba(5,150,105,.2);display:inline-flex;align-items:center;gap:7px;" onclick="startBroadcast()">
                <svg width="14" height="14" viewBox="0 0 15 15" fill="none">
                    <path d="M5 3.5l7 4-7 4V3.5z" fill="currentColor"/>
                </svg>
                Mulai Siaran
            </button>
        </div>
    </div>
</div>



{{-- ════════════════════ MODAL SIARAN SELESAI ════════════════════ --}}
<div class="mbg" id="m-bc-done">
    <div class="modal" style="text-align:center;">
        <div class="done-check">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="11" fill="url(#done-grad)" opacity=".15"/>
                <path d="M7 12.5l3.5 3.5 6.5-7" stroke="#059669" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                <defs>
                    <linearGradient id="done-grad" x1="0" y1="0" x2="24" y2="24" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#059669"/>
                        <stop offset="1" stop-color="#047857"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
        <div style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:6px;">Siaran Telah Selesai!</div>
        <div style="font-size:13px;color:#64748b;margin-bottom:20px;" id="bc-done-summary">—</div>
        <div style="display:flex;gap:8px;justify-content:center;">
            <button class="bsv" style="padding:10px 28px;border-radius:10px;font-size:14px;background:linear-gradient(135deg,#059669,#047857);box-shadow:0 2px 8px rgba(5,150,105,.2);" onclick="closeDoneModal()">Selesai</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const RBPHONE   = '{{ url('penyiar/narasumber-byphone') }}';
const RTALKSHOW = '{{ url('penyiar/narasumber-talkshow') }}';
const RBPSEARCH = '{{ route('penyiar.byphone.search') }}';
const RTSSEARCH = '{{ route('penyiar.talkshow.search') }}';

const SCHEDULE_LABELS = {
    'pendidikan_kota_kita': 'Pendidikan Kota Kita',
    'youth_speak_up':       'Youth Speak Up',
    'layanan_publik':       'Layanan Publik',
    'anda_dan_keluarga':    'Anda dan Keluarga',
    'dinkes_menyapa':       'Dinkes Menyapa',
    'komunitas_bicara':     'Komunitas Bicara',
    'indie_vibes':          'Indie Vibes',
    'kojah_sastra':         'Kojah Sastra',
    'inspirasi_bisnis':     'Inspirasi Bisnis',
    'obrolan_agama_islam':  'Obrolan Seputar Agama Islam',
    'ngosasi':              'Ngosasi (Ngobrol Asik Sana Sini)',
};

function schedBadgeHTML(key) {
    if (!key) return '<span class="sched-none">—</span>';
    return `<span class="sched-badge">${SCHEDULE_LABELS[key] || key}</span>`;
}

function bcastStatusBadge(status) {
    if (status === 'sedang_siaran') return '<span class="bdg-siaran-aktif"><span class="bdg-siaran-dot"></span>Sedang Siaran</span>';
    if (status === 'sudah_siaran')  return '<span class="bdg-siaran-selesai">✅ Sudah Siaran</span>';
    return '<span class="bdg-siaran-belum">⏳ Belum Siaran</span>';
}

function debounce(fn, delay) {
    let t;
    return function(...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), delay); };
}

// ── WhatsApp helper ────────────────────────────────────
const WA_SVG = '<svg width="13" height="13" viewBox="0 0 24 24" fill="#25d366" style="vertical-align:-2px;flex-shrink:0;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>';
function waLink(phone) {
    if (!phone) return '#';
    let num = phone.replace(/\D/g, '');
    if (num.startsWith('0')) num = '62' + num.slice(1);
    else if (!num.startsWith('62')) num = '62' + num;
    return 'https://wa.me/' + num;
}
function waLinkHTML(phone) {
    if (!phone) return '—';
    return `<a href="${waLink(phone)}" target="_blank" rel="noopener" class="wa-link">${WA_SVG}${phone}</a>`;
}

function renderPagination(d, fnName) {
    const info  = d.total === 0
        ? `<div class="pag-info-text">Tidak ada data yang ditampilkan</div>`
        : `<div class="pag-info-text">Menampilkan ${d.from} sampai ${d.to} dari ${d.total} data</div>`;

    if (d.last_page <= 1) return info;

    let html = `<div class="pagination-container">`;
    if (d.current_page > 1) {
        html += `<button class="page-btn" onclick="${fnName}(${d.current_page - 1})">«</button>`;
    }

    for (let i = 1; i <= d.last_page; i++) {
        if (i === 1 || i === d.last_page || (i >= d.current_page - 2 && i <= d.current_page + 2)) {
            let active = i === d.current_page ? 'active' : '';
            html += `<button class="page-btn ${active}" onclick="${fnName}(${i})">${i}</button>`;
        } else if (i === d.current_page - 3 || i === d.current_page + 3) {
            html += `<span class="page-dots">...</span>`;
        }
    }

    if (d.current_page < d.last_page) {
        html += `<button class="page-btn" onclick="${fnName}(${d.current_page + 1})">»</button>`;
    }
    html += `</div>`;
    return info + html;
}

// ── Byphone search ─────────────────────────────────────
let bpCurrentPage = 1;
function doSearchBp(page = null) {
    if (page !== null) bpCurrentPage = page;
    else bpCurrentPage = 1;

    const kw = document.getElementById('bp-search').value;
    const tg = document.getElementById('bp-tanggal').value;
    const lim = document.getElementById('bp-limit').value;
    const url = RBPSEARCH + '?page=' + bpCurrentPage + '&keyword=' + encodeURIComponent(kw) + (tg ? '&tanggal=' + tg : '') + '&limit=' + lim;
    fetch(url, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            const rows = d.data.map((n, i) => `
                <tr>
                    <td class="mono">${(bpCurrentPage - 1) * parseInt(document.getElementById('bp-limit').value) + i + 1}</td>
                    <td><div style="font-weight:600;">${n.nama}</div></td>
                    <td style="font-size:12px;color:#64748b;">${n.instansi}</td>
                    <td class="mono">${waLinkHTML(n.nomor_telp)}</td>
                    <td>${n.tema_wawancara}</td>
                    <td class="mono">${formatDateTime(n.waktu_wawancara)}</td>
                    <td>${schedBadgeHTML(n.byphone_schedule)}</td>
                    <td>${bcastStatusBadge(n.broadcast_status || 'belum_siaran')}</td>
                    <td><button class="bico" onclick="viewBp(${n.byphoneID})">
                        <svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                    </button></td>
                </tr>`).join('');
            document.getElementById('bp-tbody').innerHTML = rows || '<tr><td colspan="9" class="empty">Tidak ada data.</td></tr>';
            document.getElementById('bp-count').textContent = d.total + ' data';
            document.getElementById('bp-pag').innerHTML = renderPagination(d, 'doSearchBp');
        });
}

// ── Talkshow search ────────────────────────────────────
let tsCurrentPage = 1;
function doSearchTs(page = null) {
    if (page !== null) tsCurrentPage = page;
    else tsCurrentPage = 1;

    const kw = document.getElementById('ts-search').value;
    const tg = document.getElementById('ts-tanggal').value;
    const lim = document.getElementById('ts-limit').value;
    const url = RTSSEARCH + '?page=' + tsCurrentPage + '&keyword=' + encodeURIComponent(kw) + (tg ? '&tanggal=' + tg : '') + '&limit=' + lim;
    fetch(url, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            const rows = d.data.map((n, i) => `
                <tr>
                    <td class="mono">${(tsCurrentPage - 1) * parseInt(document.getElementById('ts-limit').value) + i + 1}</td>
                    <td><div style="font-weight:600;">${n.nama}</div></td>
                    <td style="font-size:12px;color:#64748b;">${n.instansi}</td>
                    <td class="mono">${waLinkHTML(n.nomor_telp)}</td>
                    <td>${n.tema_wawancara}</td>
                    <td class="mono">${formatDateTime(n.waktu_wawancara)}</td>
                    <td>${schedBadgeHTML(n.talkshow_schedule)}</td>
                    <td>${bcastStatusBadge(n.broadcast_status || 'belum_siaran')}</td>
                    <td><button class="bico" onclick="viewTs(${n.talkshowID})">
                        <svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                    </button></td>
                </tr>`).join('');
            document.getElementById('ts-tbody').innerHTML = rows || '<tr><td colspan="9" class="empty">Tidak ada data.</td></tr>';
            document.getElementById('ts-count').textContent = d.total + ' data';
            document.getElementById('ts-pag').innerHTML = renderPagination(d, 'doSearchTs');
        });
}

// ── View details ───────────────────────────────────────
function viewBp(id) {
    apiFetch(RBPHONE + '/' + id).then(d => {
        if (!d.success) return;
        const n = d.data;
        document.getElementById('bv-nama').textContent     = n.nama;
        document.getElementById('bv-instansi').textContent = n.instansi;
        document.getElementById('bv-nomor').innerHTML      = waLinkHTML(n.nomor_telp);
        document.getElementById('bv-tema').textContent     = n.tema_wawancara;
        document.getElementById('bv-waktu').textContent    = formatDateTime(n.waktu_wawancara);
        document.getElementById('bv-draft').textContent    = n.draft_pertanyaan || '(kosong)';
        openModal('m-bp-view');
    });
}
function viewTs(id) {
    apiFetch(RTALKSHOW + '/' + id).then(d => {
        if (!d.success) return;
        const n = d.data;
        document.getElementById('tv-nama').textContent     = n.nama;
        document.getElementById('tv-instansi').textContent = n.instansi;
        document.getElementById('tv-nomor').innerHTML      = waLinkHTML(n.nomor_telp);
        document.getElementById('tv-tema').textContent     = n.tema_wawancara;
        document.getElementById('tv-waktu').textContent    = formatDateTime(n.waktu_wawancara);
        document.getElementById('tv-jadwal').innerHTML     = schedBadgeHTML(n.talkshow_schedule);
        document.getElementById('tv-draft').textContent    = n.draft_pertanyaan || '(kosong)';
        openModal('m-ts-view');
    });
}

const BULAN_ID = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
function formatDateTime(d) {
    if (!d) return '—';
    const dt = new Date(d.replace(' ', 'T'));
    const tgl = dt.getDate() + '/' + BULAN_ID[dt.getMonth()] + '/' + dt.getFullYear();
    const jam = String(dt.getHours()).padStart(2,'0') + ':' + String(dt.getMinutes()).padStart(2,'0');
    return tgl + ' ' + jam;
}

// ══════════════════════════════════════════════════════
// BROADCAST
// ══════════════════════════════════════════════════════
const RBROADCAST_START   = '{{ route('penyiar.broadcast.start') }}';
const RBROADCAST_END     = '{{ route('penyiar.broadcast.end') }}';
const RBROADCAST_CURRENT = '{{ route('penyiar.broadcast.current') }}';
const RBSEARCH_BP        = '{{ route('penyiar.byphone.search') }}';
const RBSEARCH_TS        = '{{ route('penyiar.talkshow.search') }}';

// Timer
let bcTimerInterval = null;
let bcStartedAt     = null;

@if($isOnAir)
bcStartedAt = new Date('{{ $activeBroadcast->started_at->toIso8601String() }}');
startBroadcastTimer();
@endif

function startBroadcastTimer() {
    if (bcTimerInterval) clearInterval(bcTimerInterval);
    updateBroadcastTimer();
    bcTimerInterval = setInterval(updateBroadcastTimer, 1000);
}

function updateBroadcastTimer() {
    if (!bcStartedAt) return;
    const elapsed = Math.floor((Date.now() - bcStartedAt.getTime()) / 1000);
    const h = String(Math.floor(elapsed / 3600)).padStart(2, '0');
    const m = String(Math.floor((elapsed % 3600) / 60)).padStart(2, '0');
    const s = String(elapsed % 60).padStart(2, '0');
    const el = document.getElementById('bc-timer');
    if (el) el.textContent = `${h}:${m}:${s}`;
}

function formatDuration(seconds) {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;
    if (h > 0) return `${h} jam ${m} menit ${s} detik`;
    if (m > 0) return `${m} menit ${s} detik`;
    return `${s} detik`;
}

// Load narasumber dropdown options – selalu muat ulang dari server (dengan filter hari ini)
function loadNarasumberOptions() {
    const type = document.getElementById('bc-type').value;
    const sel  = document.getElementById('bc-narasumber');
    sel.innerHTML = '<option value="">— Memuat… —</option>';
    document.getElementById('bc-preview').style.display = 'none';

    const url = (type === 'byphone' ? RBSEARCH_BP : RBSEARCH_TS) + '?today_only=true';
    fetch(url, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                populateNarasumberSelect(d.data);
            } else {
                sel.innerHTML = '<option value="">— Gagal memuat data —</option>';
            }
        })
        .catch(() => {
            sel.innerHTML = '<option value="">— Gagal memuat data —</option>';
        });
}

function populateNarasumberSelect(data) {
    const sel = document.getElementById('bc-narasumber');
    if (data.length === 0) {
        sel.innerHTML = '<option value="">— Tidak ada narasumber hari ini —</option>';
        return;
    }
    sel.innerHTML = '<option value="">— Pilih narasumber —</option>';
    data.forEach(n => {
        const idKey  = n.byphoneID !== undefined ? n.byphoneID : n.talkshowID;
        const status = n.broadcast_status || 'belum_siaran';
        const opt    = document.createElement('option');
        opt.value    = idKey;

        let label = `${n.nama} — ${n.instansi}`;
        if (status === 'sudah_siaran') {
            label = `✅ ${n.nama} — ${n.instansi} (Sudah Siaran)`;
            opt.style.color = '#64748b';
        } else if (status === 'sedang_siaran') {
            label    = `🔴 ${n.nama} — ${n.instansi} (Sedang Siaran)`;
            opt.disabled = true;
            opt.style.color = '#94a3b8';
        }

        opt.textContent      = label;
        opt.dataset.nama     = n.nama;
        opt.dataset.tema     = n.tema_wawancara;
        opt.dataset.status   = status;
        sel.appendChild(opt);
    });
    sel.onchange = () => {
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.value) {
            document.getElementById('bc-prev-nama').textContent = opt.dataset.nama;
            document.getElementById('bc-prev-tema').textContent = opt.dataset.tema;
            document.getElementById('bc-preview').style.display = 'block';
        } else {
            document.getElementById('bc-preview').style.display = 'none';
        }
    };
}

// Open start broadcast modal – muat ulang data hari ini setiap kali modal dibuka
function openStartModal() {
    document.getElementById('bc-type').value = 'byphone';
    document.getElementById('bc-preview').style.display = 'none';
    loadNarasumberOptions();
    openModal('m-bc-start');
}

// Start broadcast
function startBroadcast() {
    const type = document.getElementById('bc-type').value;
    const id   = document.getElementById('bc-narasumber').value;
    if (!id) { showFlash('Pilih narasumber terlebih dahulu.', 'error'); return; }

    apiFetch(RBROADCAST_START, 'POST', { narasumber_type: type, narasumber_id: parseInt(id) })
        .then(d => {
            if (!d.success) { showFlash(d.message || 'Gagal memulai siaran.', 'error'); return; }
            closeModal('m-bc-start');
            location.reload();
        })
        .catch(() => showFlash('Terjadi kesalahan, coba lagi.', 'error'));
}

// End broadcast
function endBroadcast() {
    const notes = '';
    apiFetch(RBROADCAST_END, 'POST', { notes })
        .then(d => {
            if (!d.success) { showFlash(d.message || 'Gagal mengakhiri siaran.', 'error'); return; }
            if (bcTimerInterval) clearInterval(bcTimerInterval);
            const sess = d.data;
            const nar  = sess.narasumber || {};
            const dur  = formatDuration(sess.duration_seconds || 0);
            document.getElementById('bc-done-summary').textContent =
                `Narasumber: ${nar.nama || '—'} · Durasi: ${dur}`;
            openModal('m-bc-done');
        })
        .catch(() => showFlash('Terjadi kesalahan, coba lagi.', 'error'));
}

function closeDoneModal() {
    closeModal('m-bc-done');
    location.reload();
}

// ── Custom date filter helpers ──────────────────────────
const BULAN_FILTER = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
function syncDateDisplay(lblId, wrapId, rawValue) {
    const lbl  = document.getElementById(lblId);
    const wrap = document.getElementById(wrapId);
    const btn  = wrap ? wrap.querySelector('.date-clear-btn') : null;
    if (rawValue) {
        const dt = new Date(rawValue + 'T00:00:00');
        lbl.textContent = dt.getDate() + '/' + BULAN_FILTER[dt.getMonth()] + '/' + dt.getFullYear();
        if (wrap) wrap.classList.add('has-value');
        if (btn)  btn.style.display = 'inline';
    } else {
        lbl.textContent = 'Semua tanggal';
        if (wrap) wrap.classList.remove('has-value');
        if (btn)  btn.style.display = 'none';
    }
}
function triggerDatePicker(inputId) {
    const inp = document.getElementById(inputId);
    if (inp) { if (inp.showPicker) inp.showPicker(); else inp.click(); }
}
function clearDateFilter(inputId, lblId, wrapId, searchFn) {
    document.getElementById(inputId).value = '';
    syncDateDisplay(lblId, wrapId, '');
    searchFn();
}

document.addEventListener('DOMContentLoaded', () => {
    doSearchBp(1);
    doSearchTs(1);
});
</script>
@endpush
