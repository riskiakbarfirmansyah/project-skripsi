@extends('layouts.penyiar')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Penyiar')
@section('page-sub', 'Data narasumber byphone & talkshow')

@push('styles')
<style>
.wa-link { display:inline-flex; align-items:center; gap:4px; color:#16a34a; text-decoration:none; font-weight:500; transition:color .15s; }
.wa-link:hover { color:#15803d; text-decoration:underline; }

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
    padding:9px 20px; font-size:13px; font-weight:600;
    border-radius:8px; border:none; cursor:pointer; color:#fff;
    background:#059669; transition:opacity .15s;
}
.btn-siap:hover { opacity:.88; }
.btn-selesai {
    padding:9px 20px; font-size:13px; font-weight:600;
    border-radius:8px; border:none; cursor:pointer; color:#fff;
    background:#dc2626; transition:opacity .15s;
}
.btn-selesai:hover { opacity:.88; }

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

/* ── Notes modal ── */
.notes-ta {
    width:100%; padding:9px 12px; font-size:13px;
    border:1px solid #cbd5e1; border-radius:7px; outline:none;
    resize:vertical; min-height:80px; color:#0f172a;
}
.notes-ta:focus { border-color:#6ee7b7; box-shadow:0 0 0 3px #ecfdf5; }
.done-check {
    width:52px; height:52px; border-radius:50%; background:#d1fae5;
    display:flex; align-items:center; justify-content:center; margin:0 auto 12px;
}
</style>
@endpush

@section('content')

{{-- ════════════════════ BROADCAST STATUS CARD ════════════════════ --}}
@php $isOnAir = isset($activeBroadcast) && $activeBroadcast !== null; @endphp

<div class="bc-card {{ $isOnAir ? 'on-air' : '' }}" id="bc-card">
    <div class="bc-info">
        <div class="bc-status-dot {{ $isOnAir ? 'live' : '' }}" id="bc-dot"></div>
        <div>
            <div class="bc-label" id="bc-label">{{ $isOnAir ? '🔴 Sedang Siaran' : 'Siap Siaran' }}</div>
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
        <button class="btn-selesai" onclick="openEndModal()">Siaran Selesai</button>
    @else
        <button class="btn-siap" id="btn-siap" onclick="openStartModal()">Siap Siaran</button>
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

{{-- ── Stats ── --}}
<div class="sg">
    <div class="sc">
        <div class="sc-ico" style="background:#d1fae5;">
            <svg width="16" height="16" viewBox="0 0 15 15" fill="none">
                <path d="M5 1.5C4.5 1.5 4 2 4 2.5L3 5c-.5 1 0 2 1 2.5 1.5 1 3.5 3 4.5 4.5.5 1 1.5 1.5 2.5 1L13 12c.5-.5.5-1.5 0-2L11 8.5c-.5-.5-1.5-.5-2 0l-.5.5C8 8.5 7 7.5 6.5 7l.5-.5c.5-.5.5-1.5 0-2L5.5 2c-.5-.5-1-.5-0.5-.5z"
                      stroke="#065f46" stroke-width="1.3" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="sc-lbl">Total Byphone</div>
        <div class="sc-val">{{ $stats['total_byphone'] }}</div>
        <div class="sc-sub">Narasumber by phone</div>
    </div>
    <div class="sc">
        <div class="sc-ico" style="background:#dbeafe;">
            <svg width="16" height="16" viewBox="0 0 15 15" fill="none">
                <rect x="1.5" y="1.5" width="12" height="12" rx="1.5" stroke="#1e40af" stroke-width="1.3"/>
                <path d="M4.5 8l2 2 4-4" stroke="#1e40af" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="sc-lbl">Total Talkshow</div>
        <div class="sc-val">{{ $stats['total_talkshow'] }}</div>
        <div class="sc-sub">Narasumber talkshow</div>
    </div>
    <div class="sc">
        <div class="sc-ico" style="background:#fef3c7;">
            <svg width="16" height="16" viewBox="0 0 15 15" fill="none">
                <circle cx="7.5" cy="7.5" r="5.5" stroke="#92400e" stroke-width="1.3"/>
                <path d="M7.5 4.5v3l2 2" stroke="#92400e" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="sc-lbl">Byphone Hari Ini</div>
        <div class="sc-val">{{ $stats['byphone_hari_ini'] }}</div>
        <div class="sc-sub">Jadwal hari ini</div>
    </div>
    <div class="sc">
        <div class="sc-ico" style="background:#fce7f3;">
            <svg width="16" height="16" viewBox="0 0 15 15" fill="none">
                <circle cx="7.5" cy="7.5" r="5.5" stroke="#9d174d" stroke-width="1.3"/>
                <path d="M7.5 4.5v3l2 2" stroke="#9d174d" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="sc-lbl">Talkshow Hari Ini</div>
        <div class="sc-val">{{ $stats['talkshow_hari_ini'] }}</div>
        <div class="sc-sub">Jadwal hari ini</div>
    </div>
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
                <input type="text" id="bp-search" placeholder="Cari nama, instansi…" oninput="debounce(doSearchBp, 350)()">
            </div>
            <input type="date" class="fsel" id="bp-tanggal" onchange="doSearchBp()">
            <span class="rc" id="bp-count">{{ $byphoneList->count() }} data</span>
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Nama</th>
                        <th>Instansi</th>
                        <th style="width:120px">Telepon</th>
                        <th>Tema Wawancara</th>
                        <th style="width:115px">Waktu</th>
                        <th style="width:50px">Aksi</th>
                    </tr>
                </thead>
                <tbody id="bp-tbody">
                    @forelse($byphoneList as $i => $n)
                    <tr>
                        <td class="mono">{{ $i + 1 }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:9px;">
                                <div class="ini">{{ strtoupper(substr($n->nama, 0, 2)) }}</div>
                                <div style="font-weight:600;font-size:13px;">{{ $n->nama }}</div>
                            </div>
                        </td>
                        <td style="font-size:12px;color:#64748b;">{{ $n->instansi }}</td>
                        <td class="mono"><a href="{{ wa_link($n->nomor_telp) }}" target="_blank" rel="noopener" class="wa-link"><svg width="13" height="13" viewBox="0 0 24 24" fill="#25d366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>{{ $n->nomor_telp }}</a></td>
                        <td class="trunc" style="max-width:180px;">{{ $n->tema_wawancara }}</td>
                        <td class="mono">{{ \Carbon\Carbon::parse($n->waktu_wawancara)->format('d M Y H:i') }}</td>
                        <td>
                            <button class="bico" title="Detail" onclick="viewBp({{ $n->byphoneID }})">
                                <svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty">Belum ada data narasumber byphone.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pag" id="bp-pag">Menampilkan {{ $byphoneList->count() }} data</div>
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
                <input type="text" id="ts-search" placeholder="Cari nama, instansi…" oninput="debounce(doSearchTs, 350)()">
            </div>
            <input type="date" class="fsel" id="ts-tanggal" onchange="doSearchTs()">
            <span class="rc" id="ts-count">{{ $talkshowList->count() }} data</span>
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Nama</th>
                        <th>Instansi</th>
                        <th style="width:120px">Telepon</th>
                        <th>Tema Wawancara</th>
                        <th style="width:115px">Waktu</th>
                        <th style="width:50px">Aksi</th>
                    </tr>
                </thead>
                <tbody id="ts-tbody">
                    @forelse($talkshowList as $i => $n)
                    <tr>
                        <td class="mono">{{ $i + 1 }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:9px;">
                                <div class="ini">{{ strtoupper(substr($n->nama, 0, 2)) }}</div>
                                <div style="font-weight:600;font-size:13px;">{{ $n->nama }}</div>
                            </div>
                        </td>
                        <td style="font-size:12px;color:#64748b;">{{ $n->instansi }}</td>
                        <td class="mono"><a href="{{ wa_link($n->nomor_telp) }}" target="_blank" rel="noopener" class="wa-link"><svg width="13" height="13" viewBox="0 0 24 24" fill="#25d366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>{{ $n->nomor_telp }}</a></td>
                        <td class="trunc" style="max-width:180px;">{{ $n->tema_wawancara }}</td>
                        <td class="mono">{{ \Carbon\Carbon::parse($n->waktu_wawancara)->format('d M Y H:i') }}</td>
                        <td>
                            <button class="bico" title="Detail" onclick="viewTs({{ $n->talkshowID }})">
                                <svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty">Belum ada data narasumber talkshow.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pag" id="ts-pag">Menampilkan {{ $talkshowList->count() }} data</div>
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
        </div>
        <div id="bc-preview" style="display:none;background:#f8fafc;border-radius:8px;padding:12px 14px;margin-bottom:13px;font-size:13px;">
            <div style="font-weight:600;color:#0f172a;margin-bottom:4px;" id="bc-prev-nama">—</div>
            <div style="color:#64748b;font-size:12px;" id="bc-prev-tema">—</div>
        </div>
        <div class="mft">
            <button class="bcn" onclick="closeModal('m-bc-start')">Batal</button>
            <button class="bsv" onclick="startBroadcast()">Mulai Siaran</button>
        </div>
    </div>
</div>

{{-- ════════════════════ MODAL SELESAI SIARAN ════════════════════ --}}
<div class="mbg" id="m-bc-end">
    <div class="modal">
        <div class="mttl">
            Akhiri Siaran
            <button class="mclose" onclick="closeModal('m-bc-end')">×</button>
        </div>
        <p style="font-size:13px;color:#64748b;margin-bottom:14px;">Apakah Anda yakin ingin mengakhiri siaran?</p>
        <div class="fg">
            <label class="fl">Catatan Siaran <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
            <textarea class="notes-ta" id="bc-notes" placeholder="Tambahkan catatan siaran…"></textarea>
        </div>
        <div class="mft">
            <button class="bcn" onclick="closeModal('m-bc-end')">Batal</button>
            <button class="btn-selesai" onclick="endBroadcast()">Ya, Akhiri Siaran</button>
        </div>
    </div>
</div>

{{-- ════════════════════ MODAL SIARAN SELESAI ════════════════════ --}}
<div class="mbg" id="m-bc-done">
    <div class="modal" style="text-align:center;">
        <div class="done-check">
            <svg width="22" height="22" viewBox="0 0 15 15" fill="none">
                <path d="M12.5 4.5l-7 7-3-3" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:6px;">Siaran Telah Selesai!</div>
        <div style="font-size:13px;color:#64748b;margin-bottom:18px;" id="bc-done-summary">—</div>
        <div style="display:flex;gap:8px;justify-content:center;">
            <button class="bsv" onclick="closeDoneModal()">Selesai</button>
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

// ── Byphone search ─────────────────────────────────────
function doSearchBp() {
    const kw = document.getElementById('bp-search').value;
    const tg = document.getElementById('bp-tanggal').value;
    const url = RBPSEARCH + '?keyword=' + encodeURIComponent(kw) + (tg ? '&tanggal=' + tg : '');
    fetch(url, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            const rows = d.data.map((n, i) => `
                <tr>
                    <td class="mono">${i+1}</td>
                    <td><div style="display:flex;align-items:center;gap:9px;"><div class="ini">${ini(n.nama)}</div><div style="font-weight:600;">${n.nama}</div></div></td>
                    <td style="font-size:12px;color:#64748b;">${n.instansi}</td>
                    <td class="mono">${waLinkHTML(n.nomor_telp)}</td>
                    <td class="trunc" style="max-width:180px;">${n.tema_wawancara}</td>
                    <td class="mono">${formatDateTime(n.waktu_wawancara)}</td>
                    <td><button class="bico" onclick="viewBp(${n.byphoneID})">
                        <svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                    </button></td>
                </tr>`).join('');
            document.getElementById('bp-tbody').innerHTML = rows || '<tr><td colspan="7" class="empty">Tidak ada data.</td></tr>';
            document.getElementById('bp-count').textContent = d.total + ' data';
            document.getElementById('bp-pag').textContent = 'Menampilkan ' + d.total + ' data';
        });
}

// ── Talkshow search ────────────────────────────────────
function doSearchTs() {
    const kw = document.getElementById('ts-search').value;
    const tg = document.getElementById('ts-tanggal').value;
    const url = RTSSEARCH + '?keyword=' + encodeURIComponent(kw) + (tg ? '&tanggal=' + tg : '');
    fetch(url, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            const rows = d.data.map((n, i) => `
                <tr>
                    <td class="mono">${i+1}</td>
                    <td><div style="display:flex;align-items:center;gap:9px;"><div class="ini">${ini(n.nama)}</div><div style="font-weight:600;">${n.nama}</div></div></td>
                    <td style="font-size:12px;color:#64748b;">${n.instansi}</td>
                    <td class="mono">${waLinkHTML(n.nomor_telp)}</td>
                    <td class="trunc" style="max-width:180px;">${n.tema_wawancara}</td>
                    <td class="mono">${formatDateTime(n.waktu_wawancara)}</td>
                    <td><button class="bico" onclick="viewTs(${n.talkshowID})">
                        <svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                    </button></td>
                </tr>`).join('');
            document.getElementById('ts-tbody').innerHTML = rows || '<tr><td colspan="7" class="empty">Tidak ada data.</td></tr>';
            document.getElementById('ts-count').textContent = d.total + ' data';
            document.getElementById('ts-pag').textContent = 'Menampilkan ' + d.total + ' data';
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
        document.getElementById('tv-draft').textContent    = n.draft_pertanyaan || '(kosong)';
        openModal('m-ts-view');
    });
}

function formatDateTime(d) {
    if (!d) return '—';
    const dt = new Date(d.replace(' ', 'T'));
    return dt.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' })
        + ' ' + dt.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
}

// ══════════════════════════════════════════════════════
// BROADCAST
// ══════════════════════════════════════════════════════
const RBROADCAST_START   = '{{ route('penyiar.broadcast.start') }}';
const RBROADCAST_END     = '{{ route('penyiar.broadcast.end') }}';
const RBROADCAST_CURRENT = '{{ route('penyiar.broadcast.current') }}';
const RBSEARCH_BP        = '{{ route('penyiar.byphone.search') }}';
const RBSEARCH_TS        = '{{ route('penyiar.talkshow.search') }}';

// Storage for narasumber options by type
let bcNarasumberData = { byphone: [], talkshow: [] };

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

// Load narasumber dropdown options
function loadNarasumberOptions() {
    const type  = document.getElementById('bc-type').value;
    const sel   = document.getElementById('bc-narasumber');
    sel.innerHTML = '<option value="">— Memuat… —</option>';
    document.getElementById('bc-preview').style.display = 'none';

    if (bcNarasumberData[type].length > 0) {
        populateNarasumberSelect(bcNarasumberData[type]);
        return;
    }

    const url = type === 'byphone' ? RBSEARCH_BP : RBSEARCH_TS;
    fetch(url, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                bcNarasumberData[type] = d.data;
                populateNarasumberSelect(d.data);
            }
        });
}

function populateNarasumberSelect(data) {
    const sel = document.getElementById('bc-narasumber');
    sel.innerHTML = '<option value="">— Pilih narasumber —</option>';
    data.forEach(n => {
        const idKey = n.byphoneID !== undefined ? n.byphoneID : n.talkshowID;
        const opt   = document.createElement('option');
        opt.value   = idKey;
        opt.textContent = `${n.nama} — ${n.instansi}`;
        opt.dataset.nama  = n.nama;
        opt.dataset.tema  = n.tema_wawancara;
        sel.appendChild(opt);
    });
    sel.onchange = () => {
        const opt = sel.options[sel.selectedIndex];
        if (opt.value) {
            document.getElementById('bc-prev-nama').textContent = opt.dataset.nama;
            document.getElementById('bc-prev-tema').textContent = opt.dataset.tema;
            document.getElementById('bc-preview').style.display = 'block';
        } else {
            document.getElementById('bc-preview').style.display = 'none';
        }
    };
}

// Open start broadcast modal – pre-load byphone
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
            const sess = d.data;
            const nar  = sess.narasumber || {};
            bcStartedAt = new Date(sess.started_at);
            renderOnAir(sess);
            startBroadcastTimer();
            showFlash('Siaran dimulai!');
        })
        .catch(() => showFlash('Terjadi kesalahan, coba lagi.', 'error'));
}

function renderOnAir(sess) {
    const nar = sess.narasumber || {};
    // card
    const card = document.getElementById('bc-card');
    card.classList.add('on-air');
    document.getElementById('bc-dot').classList.add('live');
    document.getElementById('bc-label').textContent = '🔴 Sedang Siaran';
    document.getElementById('bc-sub').textContent   = (nar.nama || '—') + ' · ' + (nar.tema_wawancara || '—');

    // Replace button area — remove siap button, add timer + selesai button
    // The easiest way after an AJAX start: reload page
    location.reload();
}

// Open end modal
function openEndModal() {
    document.getElementById('bc-notes').value = '';
    openModal('m-bc-end');
}

// End broadcast
function endBroadcast() {
    const notes = document.getElementById('bc-notes').value;
    apiFetch(RBROADCAST_END, 'POST', { notes })
        .then(d => {
            if (!d.success) { showFlash(d.message || 'Gagal mengakhiri siaran.', 'error'); return; }
            closeModal('m-bc-end');
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
</script>
@endpush
