@extends('layouts.gatekeeper')

@section('title', 'Dashboard')
@section('page-title', '')
@section('page-sub', 'Manajemen data siaran dan narasumber')

@push('styles')
<style>
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
.tab-btn:hover { color: #1e40af; }
.tab-btn.active { color: #1e40af; border-bottom-color: #1e40af; }
.tab-pane { display: none; }
.tab-pane.active { display: block; }
.mttl { display: flex; justify-content: space-between; align-items: center; }
.mclose {
    background: none; border: none; cursor: pointer;
    color: #94a3b8; font-size: 18px; padding: 0; line-height: 1;
}
.mclose:hover { color: #0f172a; }
.wa-link { display:inline-flex; align-items:center; gap:4px; color:#16a34a; text-decoration:none; font-weight:500; transition:color .15s; }
.wa-link:hover { color:#15803d; text-decoration:underline; }

/* ── Siaran status badges ── */
.bdg-pending    { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#f1f5f9;color:#475569; }
.bdg-active     { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#fef2f2;color:#dc2626; }
.bdg-completed  { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#d1fae5;color:#065f46; }
.bdg-active-dot { width:6px;height:6px;border-radius:50%;background:#dc2626;animation:pulse-dot 1.4s infinite;flex-shrink:0; }
@keyframes pulse-dot {
    0%   { box-shadow:0 0 0 0 rgba(239,68,68,.4); }
    70%  { box-shadow:0 0 0 5px rgba(239,68,68,0); }
    100% { box-shadow:0 0 0 0 rgba(239,68,68,0); }
}
.btn-mulai {
    padding: 7px 11px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    color: #fff;
    background: linear-gradient(135deg, #10b981, #059669);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 1px 3px rgba(16, 185, 129, 0.15);
    white-space: nowrap;
}
.btn-mulai:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(16, 185, 129, 0.3);
    filter: brightness(1.05);
}
.btn-mulai:active { transform: translateY(0); }

.btn-selesai-s {
    padding: 7px 11px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    color: #fff;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 1px 3px rgba(239, 68, 68, 0.15);
    animation: pulseRed 1.5s ease-in-out infinite;
    white-space: nowrap;
}
.btn-selesai-s:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(239, 68, 68, 0.3);
    animation: none;
    filter: brightness(1.05);
}
.btn-selesai-s:active { transform: translateY(0); }
@keyframes pulseRed {
    0%, 100% { box-shadow: 0 1px 3px rgba(239, 68, 68, 0.15); }
    50% { box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3); }
}
.btn-mulai svg, .btn-selesai-s svg { width: 11px; height: 11px; flex-shrink: 0; }

/* ── Export modal options ── */
.exp-opt {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 14px; border-radius: 8px;
    border: 1px solid #e2e8f0; text-decoration: none;
    color: #0f172a; transition: background .15s, border-color .15s;
    cursor: pointer;
}
.exp-opt:hover { background: #f0fdf4; border-color: #86efac; }
.exp-ico { font-size: 20px; flex-shrink: 0; }
.exp-lbl { font-size: 13px; font-weight: 600; }
.exp-sub { font-size: 11px; color: #64748b; margin-top: 2px; }
/* ── Jadwal Byphone dropdown ── */
.sched-select {
    font-size: 11px; padding: 4px 6px; border-radius: 6px;
    border: 1px solid #cbd5e1; background: #f8fafc; color: #0f172a;
    cursor: pointer; min-width: 140px; max-width: 180px;
    transition: border-color .15s, background .15s;
}
.sched-select:focus { outline: none; border-color: #0f766e; background: #fff; }
.sched-select.saving { opacity: .6; pointer-events: none; }

/* ── Schedule badge (read-only) ── */
.sched-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; border-radius: 12px; font-size: 11px;
    font-weight: 600; background: #ccfbf1; color: #065f46;
}
.sched-none { color:#94a3b8; font-size:11px; }

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

/* ── Date input (form modal) ── */
.date-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #fff;
    cursor: pointer;
    width: 100%;
    box-sizing: border-box;
    transition: border-color .15s, box-shadow .15s;
    user-select: none;
}
.date-input-wrap:hover { border-color: #94a3b8; }
.date-input-wrap.has-value { border-color: #6366f1; }
.date-input-wrap input[type="date"],
.date-input-wrap input[type="datetime-local"] {
    position: absolute; opacity: 0; width: 1px; height: 1px; pointer-events: none;
}
.date-disp { font-size: 13px; color: #0f172a; flex: 1; }
.date-disp.ph { color: #94a3b8; }
</style>
@endpush

@section('sidebar-stats')
<div class="sb-stats">
    <div class="sb-stats-title">Ringkasan</div>
    <div class="sb-stats-grid">
        <div class="sb-sc">
            <div class="sb-sc-ico" style="background:#dbeafe;"><svg viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="2.8" stroke="#1e40af" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="6" stroke="#1e40af" stroke-width="1.3"/></svg></div>
            <div class="sb-sc-val" id="stat-total-siaran">{{ $stats['total_siaran'] }}</div>
            <div class="sb-sc-lbl">Siaran</div>
        </div>
        <div class="sb-sc">
            <div class="sb-sc-ico" style="background:#d1fae5;"><svg viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="4.5" r="2.5" stroke="#065f46" stroke-width="1.3"/><path d="M2 13c0-3 2.5-5 5.5-5s5.5 2 5.5 5" stroke="#065f46" stroke-width="1.3" stroke-linecap="round"/></svg></div>
            <div class="sb-sc-val" id="stat-total-narasumber">{{ $stats['total_narasumber'] }}</div>
            <div class="sb-sc-lbl">Narasumber</div>
        </div>
        <div class="sb-sc">
            <div class="sb-sc-ico" style="background:#fef3c7;"><svg viewBox="0 0 15 15" fill="none"><rect x="1.5" y="2.5" width="12" height="10" rx="1.5" stroke="#92400e" stroke-width="1.3"/><path d="M4.5 6h6M4.5 8.5h4" stroke="#92400e" stroke-width="1.2" stroke-linecap="round"/></svg></div>
            <div class="sb-sc-val" id="stat-siaran-hari-ini">{{ $stats['siaran_hari_ini'] }}</div>
            <div class="sb-sc-lbl">Siaran Ini</div>
        </div>
        <div class="sb-sc">
            <div class="sb-sc-ico" style="background:#fce7f3;"><svg viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="5.5" stroke="#9d174d" stroke-width="1.3"/><path d="M7.5 4.5v3l2 2" stroke="#9d174d" stroke-width="1.3" stroke-linecap="round"/></svg></div>
            <div class="sb-sc-val" id="stat-wawancara-hari-ini">{{ $stats['wawancara_hari_ini'] }}</div>
            <div class="sb-sc-lbl">Byphone Ini</div>
        </div>
    </div>
</div>
@endsection

@section('content')

{{-- ── Tabs ── --}}
<div class="tabs">
    <button class="tab-btn active" id="tab-siaran-btn" onclick="showTab('siaran')">
        Informasi Siaran
    </button>
    <button class="tab-btn" id="tab-narasumber-btn" onclick="showTab('narasumber')">
        Narasumber Byphone
    </button>
</div>

{{-- ════════════════════ TAB: SIARAN ════════════════════ --}}
<div class="tab-pane active" id="tab-siaran">
    <div class="tc">
        <div class="tbar">
            <div class="sw">
                <svg viewBox="0 0 15 15" fill="none">
                    <circle cx="6.5" cy="6.5" r="4" stroke="currentColor" stroke-width="1.3"/>
                    <path d="M11 11l2.5 2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                </svg>
                <input type="text" id="siaran-search" placeholder="Cari keterangan…" oninput="debounce(doSearchSiaran, 350)()">
            </div>
            <span style="font-size:12px;color:#64748b;white-space:nowrap;">Filter tanggal:</span>
            <div class="date-filter-wrap" id="siaran-tanggal-wrap" onclick="triggerDatePicker('siaran-tanggal')">
                <svg width="13" height="13" viewBox="0 0 15 15" fill="none"><rect x="1.5" y="2.5" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M10 1v3M1.5 6h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                <span id="siaran-tanggal-lbl">Semua tanggal</span>
                <button type="button" class="date-clear-btn" style="display:none" onclick="event.stopPropagation();clearDateFilter('siaran-tanggal','siaran-tanggal-lbl','siaran-tanggal-wrap',doSearchSiaran)">×</button>
                <input type="date" id="siaran-tanggal" onchange="syncDateDisplay('siaran-tanggal-lbl','siaran-tanggal-wrap',this.value);doSearchSiaran()">
            </div>
            <select class="fsel" id="siaran-limit" onchange="doSearchSiaran()">
                <option value="10">10</option>
                <option value="15" selected>15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="rc" id="siaran-count">{{ $siaranList->count() }} data</span>
            <button class="btn-add b-blue" onclick="openModal('m-siaran-add')">
                <svg width="12" height="12" viewBox="0 0 15 15" fill="none">
                    <path d="M7.5 1v13M1 7.5h13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Tambah Siaran
            </button>
            <button class="btn-add b-green" onclick="openModal('m-export-siaran')">
                <svg width="12" height="12" viewBox="0 0 15 15" fill="none">
                    <path d="M7.5 1v9M4.5 7l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 11v1.5a.5.5 0 00.5.5h10a.5.5 0 00.5-.5V11" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                </svg>
                Export
            </button>
        </div>

        <div style="font-weight:600;color:#64748b;font-size:13px;padding:0 8px 10px;">
            Tabel Informasi Siaran
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width:60px">Nomor</th>
                        <th style="width:110px">Tanggal</th>
                        <th style="width:80px">Waktu</th>
                        <th style="min-width:200px">Keterangan</th>
                        <th style="width:110px">Status</th>
                        <th style="width:180px">Aksi</th>
                    </tr>
                </thead>
                <tbody id="siaran-tbody">
                    @forelse($siaranList as $i => $s)
                    <tr data-id="{{ $s->siaranID }}">
                        <td class="mono">{{ $siaranList->firstItem() + $i }}</td>
                        <td class="mono">{{ format_tanggal_id($s->tanggal) }}</td>
                        <td class="mono">{{ $s->waktu }}</td>
                        <td class="trunc" style="max-width:220px;">{{ $s->keterangan }}</td>
                        <td>
                            @if($s->status === 'active')
                                <span class="bdg-active"><span class="bdg-active-dot"></span>Sedang Berlangsung</span>
                            @elseif($s->status === 'completed')
                                <span class="bdg-completed">✅ Sudah Selesai</span>
                            @else
                                <span class="bdg-pending">⏰ Dijadwalkan</span>
                            @endif
                        </td>
                        <td>
                            <div class="abt">
                                @if($s->status === 'pending')
                                <button class="btn-mulai" title="Mulai Siaran" onclick="mulaiSiaran({{ $s->siaranID }})">
                                    <svg viewBox="0 0 15 15" fill="none"><path d="M3.5 2.5l9 5-9 5V2.5z" fill="currentColor"/></svg>
                                    Mulai
                                </button>
                                @elseif($s->status === 'active')
                                <button class="btn-selesai-s" title="Selesai Siaran" onclick="selesaiSiaran({{ $s->siaranID }})">
                                    <svg viewBox="0 0 15 15" fill="none"><rect x="3" y="3" width="9" height="9" rx="1.5" fill="currentColor"/></svg>
                                    Selesai
                                </button>
                                @endif
                                <button class="bico" title="Detail" onclick="viewSiaran({{ $s->siaranID }})">
                                    <svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                                </button>
                                <button class="bico" title="Edit" onclick="editSiaran({{ $s->siaranID }})">
                                    <svg viewBox="0 0 15 15" fill="none"><path d="M10.5 2.5l2 2-8 8H2.5v-2l8-8z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>
                                </button>
                                <button class="bico del" title="Hapus" onclick="deleteSiaran({{ $s->siaranID }})">
                                    <svg viewBox="0 0 15 15" fill="none"><path d="M2.5 4.5h10M6 4.5V3h3v1.5M5.5 4.5v7h4v-7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="empty">Belum ada data siaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pag" id="siaran-pag"></div>
    </div>
</div>

{{-- ════════════════════ TAB: NARASUMBER ════════════════════ --}}
<div class="tab-pane" id="tab-narasumber">
    <div class="tc">
        <div class="tbar">
            <div class="sw">
                <svg viewBox="0 0 15 15" fill="none">
                    <circle cx="6.5" cy="6.5" r="4" stroke="currentColor" stroke-width="1.3"/>
                    <path d="M11 11l2.5 2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                </svg>
                <input type="text" id="nr-search" placeholder="Cari nama, instansi, tema, jadwal…" oninput="debounce(doSearchNr, 350)()">
            </div>
            <span style="font-size:12px;color:#64748b;white-space:nowrap;">Filter tanggal:</span>
            <div class="date-filter-wrap" id="nr-tanggal-wrap" onclick="triggerDatePicker('nr-tanggal')">
                <svg width="13" height="13" viewBox="0 0 15 15" fill="none"><rect x="1.5" y="2.5" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M10 1v3M1.5 6h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                <span id="nr-tanggal-lbl">Semua tanggal</span>
                <button type="button" class="date-clear-btn" style="display:none" onclick="event.stopPropagation();clearDateFilter('nr-tanggal','nr-tanggal-lbl','nr-tanggal-wrap',doSearchNr)">×</button>
                <input type="date" id="nr-tanggal" onchange="syncDateDisplay('nr-tanggal-lbl','nr-tanggal-wrap',this.value);doSearchNr()">
            </div>
            <select class="fsel" id="nr-limit" onchange="doSearchNr()">
                <option value="10">10</option>
                <option value="15" selected>15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="rc" id="nr-count">{{ $narasumberList->count() }} data</span>
            <button class="btn-add b-teal" onclick="openModal('m-nr-add')">
                <svg width="12" height="12" viewBox="0 0 15 15" fill="none">
                    <path d="M7.5 1v13M1 7.5h13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Tambah Narasumber
            </button>
            <button class="btn-add b-green" onclick="openModal('m-export-byphone')">
                <svg width="12" height="12" viewBox="0 0 15 15" fill="none">
                    <path d="M7.5 1v9M4.5 7l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 11v1.5a.5.5 0 00.5.5h10a.5.5 0 00.5-.5V11" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                </svg>
                Export
            </button>
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
                        <th style="width:150px">Status Jadwal</th>
                        <th style="width:180px">Aksi</th>
                    </tr>
                </thead>
                <tbody id="nr-tbody">
                    @forelse($narasumberList as $i => $n)
                    <tr data-id="{{ $n->byphoneID }}">
                        <td class="mono">{{ $narasumberList->firstItem() + $i }}</td>
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
                            <div class="abt">
                                <button class="bico" title="Detail" onclick="viewNr({{ $n->byphoneID }})">
                                    <svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                                </button>
                                <button class="bico" title="Edit" onclick="editNr({{ $n->byphoneID }})">
                                    <svg viewBox="0 0 15 15" fill="none"><path d="M10.5 2.5l2 2-8 8H2.5v-2l8-8z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>
                                </button>
                                <button class="bico del" title="Hapus" onclick="deleteNr({{ $n->byphoneID }})">
                                    <svg viewBox="0 0 15 15" fill="none"><path d="M2.5 4.5h10M6 4.5V3h3v1.5M5.5 4.5v7h4v-7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="empty">Belum ada data narasumber byphone.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pag" id="nr-pag"></div>
    </div>
</div>

{{-- ════════════════════ MODALS SIARAN ════════════════════ --}}
<div class="mbg" id="m-siaran-add">
    <div class="modal">
        <div class="mttl">
            Tambah Data Siaran
            <button class="mclose" onclick="closeModal('m-siaran-add')">×</button>
        </div>
        <form onsubmit="submitSiaranAdd(event)">
            <div class="frow">
                <div class="fg">
                    <label class="fl">Tanggal</label>
                    <div class="date-input-wrap" id="sa-tanggal-wrap" onclick="triggerDatePicker('sa-tanggal')">
                        <svg width="14" height="14" viewBox="0 0 15 15" fill="none"><rect x="1.5" y="2.5" width="12" height="11" rx="1.5" stroke="#94a3b8" stroke-width="1.3"/><path d="M5 1v3M10 1v3M1.5 6h12" stroke="#94a3b8" stroke-width="1.3" stroke-linecap="round"/></svg>
                        <span class="date-disp ph" id="sa-tanggal-lbl">Pilih tanggal…</span>
                        <input type="date" id="sa-tanggal" onchange="syncFormDate('sa-tanggal-lbl','sa-tanggal-wrap',this.value)">
                    </div>
                </div>
                <div class="fg">
                    <label class="fl">Waktu</label>
                    <input type="time" class="fi" id="sa-waktu" required>
                </div>
            </div>
            <div class="fg">
                <label class="fl">Keterangan</label>
                <textarea class="fi" id="sa-keterangan" rows="4" maxlength="500" placeholder="Keterangan siaran…" required></textarea>
            </div>
            <div class="mft">
                <button type="button" class="bcn" onclick="closeModal('m-siaran-add')">Batal</button>
                <button type="submit" class="bsv" style="background:#1e40af;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="mbg" id="m-siaran-edit">
    <div class="modal">
        <div class="mttl">
            Edit Data Siaran
            <button class="mclose" onclick="closeModal('m-siaran-edit')">×</button>
        </div>
        <form onsubmit="submitSiaranEdit(event)">
            <input type="hidden" id="se-id">
            <div class="frow">
                <div class="fg">
                    <label class="fl">Tanggal</label>
                    <div class="date-input-wrap" id="se-tanggal-wrap" onclick="triggerDatePicker('se-tanggal')">
                        <svg width="14" height="14" viewBox="0 0 15 15" fill="none"><rect x="1.5" y="2.5" width="12" height="11" rx="1.5" stroke="#94a3b8" stroke-width="1.3"/><path d="M5 1v3M10 1v3M1.5 6h12" stroke="#94a3b8" stroke-width="1.3" stroke-linecap="round"/></svg>
                        <span class="date-disp ph" id="se-tanggal-lbl">Pilih tanggal…</span>
                        <input type="date" id="se-tanggal" onchange="syncFormDate('se-tanggal-lbl','se-tanggal-wrap',this.value)">
                    </div>
                </div>
                <div class="fg">
                    <label class="fl">Waktu</label>
                    <input type="time" class="fi" id="se-waktu" required>
                </div>
            </div>
            <div class="fg">
                <label class="fl">Keterangan</label>
                <textarea class="fi" id="se-keterangan" rows="4" maxlength="500" required></textarea>
            </div>
            <div class="mft">
                <button type="button" class="bcn" onclick="closeModal('m-siaran-edit')">Batal</button>
                <button type="submit" class="bsv" style="background:#1e40af;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div class="mbg" id="m-siaran-view">
    <div class="modal">
        <div class="mttl">
            Detail Siaran
            <button class="mclose" onclick="closeModal('m-siaran-view')">×</button>
        </div>
        <div class="dr"><span class="dl">Tanggal</span><span class="dv" id="sv-tanggal">—</span></div>
        <div class="dr"><span class="dl">Waktu</span><span class="dv" id="sv-waktu">—</span></div>
        <div class="dr"><span class="dl">Status</span><span class="dv" id="sv-status">—</span></div>
        <div class="dr"><span class="dl">Mulai</span><span class="dv" id="sv-started">—</span></div>
        <div class="dr"><span class="dl">Selesai</span><span class="dv" id="sv-ended">—</span></div>
        <div class="dr" style="flex-direction:column;">
            <span class="dl">Keterangan</span>
            <div class="dbox" id="sv-keterangan">—</div>
        </div>
        <div class="mft"><button class="bcn" onclick="closeModal('m-siaran-view')">Tutup</button></div>
    </div>
</div>

{{-- ════════════════════ MODALS NARASUMBER ════════════════════ --}}
<div class="mbg" id="m-nr-add">
    <div class="modal">
        <div class="mttl">
            Tambah Narasumber Byphone
            <button class="mclose" onclick="closeModal('m-nr-add')">×</button>
        </div>
        <form onsubmit="submitNrAdd(event)">
            <div class="frow">
                <div class="fg">
                    <label class="fl">Nama</label>
                    <input type="text" class="fi" id="na-nama" placeholder="Nama lengkap" required>
                </div>
                <div class="fg">
                    <label class="fl">Instansi</label>
                    <input type="text" class="fi" id="na-instansi" placeholder="Nama instansi" required>
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label class="fl">Nomor Telepon</label>
                    <input type="text" class="fi" id="na-nomor" placeholder="08xx" required>
                </div>
                <div class="fg">
                    <label class="fl">Waktu Wawancara</label>
                    <div class="date-input-wrap" id="na-waktu-wrap" onclick="triggerDatePicker('na-waktu')">
                        <svg width="14" height="14" viewBox="0 0 15 15" fill="none"><rect x="1.5" y="2.5" width="12" height="11" rx="1.5" stroke="#94a3b8" stroke-width="1.3"/><path d="M5 1v3M10 1v3M1.5 6h12" stroke="#94a3b8" stroke-width="1.3" stroke-linecap="round"/></svg>
                        <span class="date-disp ph" id="na-waktu-lbl">Pilih tanggal &amp; waktu…</span>
                        <input type="datetime-local" id="na-waktu" onchange="syncFormDatetime('na-waktu-lbl','na-waktu-wrap',this.value)">
                    </div>
                </div>
            </div>
            <div class="fg">
                <label class="fl">Tema Wawancara</label>
                <input type="text" class="fi" id="na-tema" placeholder="Topik wawancara" required>
            </div>
            <div class="fg">
                <label class="fl">Draft Pertanyaan <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
                <textarea class="fi" id="na-draft" rows="3" placeholder="Daftar pertanyaan…"></textarea>
            </div>
            <div class="mft">
                <button type="button" class="bcn" onclick="closeModal('m-nr-add')">Batal</button>
                <button type="submit" class="bsv" style="background:#0f766e;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="mbg" id="m-nr-edit">
    <div class="modal">
        <div class="mttl">
            Edit Narasumber Byphone
            <button class="mclose" onclick="closeModal('m-nr-edit')">×</button>
        </div>
        <form onsubmit="submitNrEdit(event)">
            <input type="hidden" id="ne-id">
            <div class="frow">
                <div class="fg">
                    <label class="fl">Nama</label>
                    <input type="text" class="fi" id="ne-nama" required>
                </div>
                <div class="fg">
                    <label class="fl">Instansi</label>
                    <input type="text" class="fi" id="ne-instansi" required>
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label class="fl">Nomor Telepon</label>
                    <input type="text" class="fi" id="ne-nomor" required>
                </div>
                <div class="fg">
                    <label class="fl">Waktu Wawancara</label>
                    <div class="date-input-wrap" id="ne-waktu-wrap" onclick="triggerDatePicker('ne-waktu')">
                        <svg width="14" height="14" viewBox="0 0 15 15" fill="none"><rect x="1.5" y="2.5" width="12" height="11" rx="1.5" stroke="#94a3b8" stroke-width="1.3"/><path d="M5 1v3M10 1v3M1.5 6h12" stroke="#94a3b8" stroke-width="1.3" stroke-linecap="round"/></svg>
                        <span class="date-disp ph" id="ne-waktu-lbl">Pilih tanggal &amp; waktu…</span>
                        <input type="datetime-local" id="ne-waktu" onchange="syncFormDatetime('ne-waktu-lbl','ne-waktu-wrap',this.value)">
                    </div>
                </div>
            </div>
            <div class="fg">
                <label class="fl">Tema Wawancara</label>
                <input type="text" class="fi" id="ne-tema" required>
            </div>
            <div class="fg">
                <label class="fl">Jenis Jadwal Byphone <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
                <select class="fi" id="ne-jadwal">
                    <option value="">— Pilih Jenis Jadwal —</option>
                    <option value="pendidikan_kota_kita">Pendidikan Kota Kita</option>
                    <option value="youth_speak_up">Youth Speak Up</option>
                    <option value="layanan_publik">Layanan Publik</option>
                    <option value="anda_dan_keluarga">Anda dan Keluarga</option>
                    <option value="dinkes_menyapa">Dinkes Menyapa</option>
                    <option value="komunitas_bicara">Komunitas Bicara</option>
                    <option value="indie_vibes">Indie Vibes</option>
                    <option value="kojah_sastra">Kojah Sastra</option>
                    <option value="inspirasi_bisnis">Inspirasi Bisnis</option>
                    <option value="obrolan_agama_islam">Obrolan Seputar Agama Islam</option>
                    <option value="ngosasi">Ngosasi (Ngobrol Asik Sana Sini)</option>
                </select>
            </div>
            <div class="fg">
                <label class="fl">Draft Pertanyaan <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
                <textarea class="fi" id="ne-draft" rows="3"></textarea>
            </div>
            <div class="mft">
                <button type="button" class="bcn" onclick="closeModal('m-nr-edit')">Batal</button>
                <button type="submit" class="bsv" style="background:#0f766e;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div class="mbg" id="m-nr-view">
    <div class="modal">
        <div class="mttl">
            Detail Narasumber Byphone
            <button class="mclose" onclick="closeModal('m-nr-view')">×</button>
        </div>
        <div class="dr"><span class="dl">Nama</span><span class="dv" id="nv-nama">—</span></div>
        <div class="dr"><span class="dl">Instansi</span><span class="dv" id="nv-instansi">—</span></div>
        <div class="dr"><span class="dl">Nomor Telepon</span><span class="dv" id="nv-nomor">—</span></div>
        <div class="dr"><span class="dl">Tema Wawancara</span><span class="dv" id="nv-tema">—</span></div>
        <div class="dr"><span class="dl">Waktu Wawancara</span><span class="dv" id="nv-waktu">—</span></div>
        <div class="dr" style="flex-direction:column;">
            <span class="dl">Draft Pertanyaan</span>
            <div class="dbox" id="nv-draft">—</div>
        </div>
        <div class="mft"><button class="bcn" onclick="closeModal('m-nr-view')">Tutup</button></div>
    </div>
</div>

{{-- ════════════════════ MODAL EXPORT BYPHONE ════════════════════ --}}
<div class="mbg" id="m-export-byphone">
    <div class="modal" style="max-width:360px;">
        <div class="mttl">
            Export Narasumber Byphone
            <button class="mclose" onclick="closeModal('m-export-byphone')">×</button>
        </div>
        <p style="font-size:13px;color:#64748b;margin:0 0 16px;">
            Pilih periode data yang ingin di-export. File Excel akan berisi sheet: <strong>Narasumber Byphone</strong>.
        </p>
        <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px;">
            <a id="expbp-week" href="#" class="exp-opt" onclick="doExportByphone('week')">
                <div class="exp-ico">📅</div>
                <div>
                    <div class="exp-lbl">Minggu Ini</div>
                    <div class="exp-sub">Data 7 hari terakhir</div>
                </div>
            </a>
            <a id="expbp-month" href="#" class="exp-opt" onclick="doExportByphone('month')">
                <div class="exp-ico">🗓️</div>
                <div>
                    <div class="exp-lbl">Bulan Ini</div>
                    <div class="exp-sub">Data 30 hari terakhir</div>
                </div>
            </a>
            <a id="expbp-year" href="#" class="exp-opt" onclick="doExportByphone('year')">
                <div class="exp-ico">📆</div>
                <div>
                    <div class="exp-lbl">Tahun Ini</div>
                    <div class="exp-sub">Data 365 hari terakhir</div>
                </div>
            </a>
        </div>
        <div id="expbp-loading" style="display:none;text-align:center;font-size:13px;color:#64748b;padding:8px 0;">
            ⏳ Sedang memproses export…
        </div>
        <div class="mft">
            <button class="bcn" onclick="closeModal('m-export-byphone')">Batal</button>
        </div>
    </div>
</div>

{{-- ════════════════════ MODAL EXPORT SIARAN ════════════════════ --}}
<div class="mbg" id="m-export-siaran">
    <div class="modal" style="max-width:360px;">
        <div class="mttl">
            Export Informasi Siaran
            <button class="mclose" onclick="closeModal('m-export-siaran')">×</button>
        </div>
        <p style="font-size:13px;color:#64748b;margin:0 0 16px;">
            Pilih periode data yang ingin di-export. File Excel akan berisi sheet: <strong>Informasi Siaran</strong>.
        </p>
        <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px;">
            <a id="expsr-week" href="#" class="exp-opt" onclick="doExportSiaran('week')">
                <div class="exp-ico">📅</div>
                <div>
                    <div class="exp-lbl">Minggu Ini</div>
                    <div class="exp-sub">Data 7 hari terakhir</div>
                </div>
            </a>
            <a id="expsr-month" href="#" class="exp-opt" onclick="doExportSiaran('month')">
                <div class="exp-ico">🗓️</div>
                <div>
                    <div class="exp-lbl">Bulan Ini</div>
                    <div class="exp-sub">Data 30 hari terakhir</div>
                </div>
            </a>
            <a id="expsr-year" href="#" class="exp-opt" onclick="doExportSiaran('year')">
                <div class="exp-ico">📆</div>
                <div>
                    <div class="exp-lbl">Tahun Ini</div>
                    <div class="exp-sub">Data 365 hari terakhir</div>
                </div>
            </a>
        </div>
        <div id="expsr-loading" style="display:none;text-align:center;font-size:13px;color:#64748b;padding:8px 0;">
            ⏳ Sedang memproses export…
        </div>
        <div class="mft">
            <button class="bcn" onclick="closeModal('m-export-siaran')">Batal</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const RSIARAN  = '{{ url('gatekeeper/siaran') }}';
const RBYPHONE = '{{ url('gatekeeper/narasumber-byphone') }}';
const RSTATS   = '{{ route('gatekeeper.stats') }}';
const RSRSEARCH  = '{{ route('gatekeeper.siaran.search') }}';
const RNRSEARCH  = '{{ route('gatekeeper.byphone.search') }}';
const RSIARAN_START  = (id) => `{{ url('gatekeeper/siaran') }}/${id}/start`;
const RSIARAN_END    = (id) => `{{ url('gatekeeper/siaran') }}/${id}/end`;
const RBPSCHEDULE    = '{{ url('gatekeeper/narasumber-byphone') }}';

const BYPHONE_SCHEDULE_LABELS = {
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
    return `<span class="sched-badge">${BYPHONE_SCHEDULE_LABELS[key] || key}</span>`;
}

// ── Stats refresh ──────────────────────────────────────
function updateStats() {
    fetch(RSTATS, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            const s = d.data;
            document.getElementById('stat-total-siaran').textContent       = s.total_siaran;
            document.getElementById('stat-total-narasumber').textContent   = s.total_narasumber;
            document.getElementById('stat-siaran-hari-ini').textContent    = s.siaran_hari_ini;
            document.getElementById('stat-wawancara-hari-ini').textContent = s.wawancara_hari_ini;
            const bdgS = document.getElementById('bdg-s');
            const bdgN = document.getElementById('bdg-n');
            if (bdgS) bdgS.textContent = s.total_siaran;
            if (bdgN) bdgN.textContent = s.total_narasumber;
        });
}

// ── Tab switching ─────────────────────────────────────
function showTab(name) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    document.getElementById('tab-' + name + '-btn').classList.add('active');
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

// ── Siaran Search ─────────────────────────────────────
function siaranStatusBadge(status) {
    if (status === 'active')     return '<span class="bdg-active"><span class="bdg-active-dot"></span>Sedang Berlangsung</span>';
    if (status === 'completed')  return '<span class="bdg-completed">✅ Sudah Selesai</span>';
    return '<span class="bdg-pending">⏰ Dijadwalkan</span>';
}
function siaranActionBtn(s) {
    if (s.status === 'pending')   return `<button class="btn-mulai" title="Mulai Siaran" onclick="mulaiSiaran(${s.siaranID})"><svg viewBox="0 0 15 15" fill="none"><path d="M3.5 2.5l9 5-9 5V2.5z" fill="currentColor"/></svg> Mulai</button>`;
    if (s.status === 'active')    return `<button class="btn-selesai-s" title="Selesai Siaran" onclick="selesaiSiaran(${s.siaranID})"><svg viewBox="0 0 15 15" fill="none"><rect x="3" y="3" width="9" height="9" rx="1.5" fill="currentColor"/></svg> Selesai</button>`;
    return '';
}

function renderPagination(d, fnName) {
    if (d.last_page <= 1) return `Menampilkan ${d.total} data`;
    
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
    return html;
}

let siaranCurrentPage = 1;
function doSearchSiaran(page = null) {
    if (page !== null) siaranCurrentPage = page;
    else siaranCurrentPage = 1;

    const kw = document.getElementById('siaran-search').value;
    const tg = document.getElementById('siaran-tanggal').value;
    const lim = document.getElementById('siaran-limit').value;
    const url = RSRSEARCH + '?page=' + siaranCurrentPage + '&keyword=' + encodeURIComponent(kw) + (tg ? '&tanggal=' + tg : '') + '&limit=' + lim;
    fetch(url, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            const rows = d.data.map((s, i) => `
                <tr data-id="${s.siaranID}">
                    <td class="mono">${(siaranCurrentPage - 1) * parseInt(document.getElementById('siaran-limit').value) + i + 1}</td>
                    <td class="mono">${formatDate(s.tanggal)}</td>
                    <td class="mono">${s.waktu}</td>
                    <td class="trunc" style="max-width:220px;">${s.keterangan}</td>
                    <td>${siaranStatusBadge(s.status)}</td>
                    <td><div class="abt">
                        ${siaranActionBtn(s)}
                        <button class="bico" onclick="viewSiaran(${s.siaranID})"><svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg></button>
                        <button class="bico" onclick="editSiaran(${s.siaranID})"><svg viewBox="0 0 15 15" fill="none"><path d="M10.5 2.5l2 2-8 8H2.5v-2l8-8z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg></button>
                        <button class="bico del" onclick="deleteSiaran(${s.siaranID})"><svg viewBox="0 0 15 15" fill="none"><path d="M2.5 4.5h10M6 4.5V3h3v1.5M5.5 4.5v7h4v-7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                    </div></td>
                </tr>`).join('');
            document.getElementById('siaran-tbody').innerHTML = rows || '<tr><td colspan="6" class="empty">Tidak ada data.</td></tr>';
            document.getElementById('siaran-count').textContent = d.total + ' data';
            document.getElementById('siaran-pag').innerHTML = renderPagination(d, 'doSearchSiaran');
        });
}

// ── Narasumber Search ─────────────────────────────────
let nrCurrentPage = 1;
function doSearchNr(page = null) {
    if (page !== null) nrCurrentPage = page;
    else nrCurrentPage = 1;

    const kw = document.getElementById('nr-search').value;
    const tg = document.getElementById('nr-tanggal').value;
    const lim = document.getElementById('nr-limit').value;
    const url = RNRSEARCH + '?page=' + nrCurrentPage + '&keyword=' + encodeURIComponent(kw) + (tg ? '&tanggal=' + tg : '') + '&limit=' + lim;
    fetch(url, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            const rows = d.data.map((n, i) => `
                <tr>
                    <td class="mono">${(nrCurrentPage - 1) * parseInt(document.getElementById('nr-limit').value) + i + 1}</td>
                    <td><div style="font-weight:600;">${n.nama}</div></td>
                    <td style="font-size:12px;color:#64748b;">${n.instansi}</td>
                    <td class="mono">${waLinkHTML(n.nomor_telp)}</td>
                    <td>${n.tema_wawancara}</td>
                    <td class="mono">${formatDateTime(n.waktu_wawancara)}</td>
                    <td>${schedBadgeHTML(n.byphone_schedule)}</td>
                    <td><div class="abt">
                        <button class="bico" onclick="viewNr(${n.byphoneID})"><svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg></button>
                        <button class="bico" onclick="editNr(${n.byphoneID})"><svg viewBox="0 0 15 15" fill="none"><path d="M10.5 2.5l2 2-8 8H2.5v-2l8-8z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg></button>
                        <button class="bico del" onclick="deleteNr(${n.byphoneID})"><svg viewBox="0 0 15 15" fill="none"><path d="M2.5 4.5h10M6 4.5V3h3v1.5M5.5 4.5v7h4v-7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                    </div></td>
                </tr>`).join('');
            document.getElementById('nr-tbody').innerHTML = rows || '<tr><td colspan="8" class="empty">Tidak ada data.</td></tr>';
            document.getElementById('nr-count').textContent = d.total + ' data';
            document.getElementById('nr-pag').innerHTML = renderPagination(d, 'doSearchNr');
        });
}

// ── Siaran CRUD ───────────────────────────────────────
function statusLabel(status) {
    if (status === 'active')    return 'Sedang Berlangsung';
    if (status === 'completed') return 'Sudah Selesai';
    return 'Dijadwalkan';
}
function viewSiaran(id) {
    apiFetch(RSIARAN + '/' + id).then(d => {
        if (!d.success) return;
        const s = d.data;
        document.getElementById('sv-tanggal').textContent   = formatDate(s.tanggal);
        document.getElementById('sv-waktu').textContent     = s.waktu;
        document.getElementById('sv-status').textContent    = statusLabel(s.status);
        document.getElementById('sv-started').textContent   = s.started_at ? formatDateTime(s.started_at) : '—';
        document.getElementById('sv-ended').textContent     = s.ended_at   ? formatDateTime(s.ended_at)   : '—';
        document.getElementById('sv-keterangan').textContent = s.keterangan;
        openModal('m-siaran-view');
    });
}
function editSiaran(id) {
    apiFetch(RSIARAN + '/' + id).then(d => {
        if (!d.success) return;
        document.getElementById('se-id').value        = d.data.siaranID;
        document.getElementById('se-tanggal').value   = d.data.tanggal;
        syncFormDate('se-tanggal-lbl', 'se-tanggal-wrap', d.data.tanggal);
        document.getElementById('se-waktu').value      = d.data.waktu;
        document.getElementById('se-keterangan').value = d.data.keterangan;
        openModal('m-siaran-edit');
    });
}
function submitSiaranAdd(e) {
    e.preventDefault();
    apiFetch(RSIARAN, 'POST', {
        tanggal:    document.getElementById('sa-tanggal').value,
        waktu:      document.getElementById('sa-waktu').value,
        keterangan: document.getElementById('sa-keterangan').value,
    }).then(d => {
        if (d.success) { closeModal('m-siaran-add'); document.getElementById('sa-tanggal').closest('form').reset(); showFlash(d.message); doSearchSiaran(siaranCurrentPage); updateStats(); }
        else showFlash(d.message || 'Gagal menyimpan.', 'error');
    });
}
function submitSiaranEdit(e) {
    e.preventDefault();
    const id = document.getElementById('se-id').value;
    apiFetch(RSIARAN + '/' + id, 'PUT', {
        tanggal:    document.getElementById('se-tanggal').value,
        waktu:      document.getElementById('se-waktu').value,
        keterangan: document.getElementById('se-keterangan').value,
    }).then(d => {
        if (d.success) { closeModal('m-siaran-edit'); showFlash(d.message); doSearchSiaran(siaranCurrentPage); updateStats(); }
        else showFlash(d.message || 'Gagal memperbarui.', 'error');
    });
}
function deleteSiaran(id) {
    showDeleteConfirm(
        'Hapus Data Siaran',
        'Apakah Anda yakin ingin menghapus data siaran ini? Tindakan ini tidak dapat dibatalkan.',
        function(done) {
            apiFetch(RSIARAN + '/' + id, 'DELETE').then(d => {
                done();
                if (d.success) { showFlash(d.message); doSearchSiaran(siaranCurrentPage); updateStats(); }
                else showFlash(d.message || 'Gagal menghapus.', 'error');
            }).catch(function() { done(); showFlash('Terjadi kesalahan, coba lagi.', 'error'); });
        }
    );
}
function mulaiSiaran(id) {
    apiFetch(RSIARAN_START(id), 'POST').then(d => {
        if (d.success) { showFlash(d.message); doSearchSiaran(siaranCurrentPage); updateStats(); }
        else showFlash(d.message || 'Gagal memulai siaran.', 'error');
    });
}
function selesaiSiaran(id) {
    apiFetch(RSIARAN_END(id), 'POST').then(d => {
        if (d.success) { showFlash(d.message); doSearchSiaran(siaranCurrentPage); updateStats(); }
        else showFlash(d.message || 'Gagal mengakhiri siaran.', 'error');
    });
}

// ── Narasumber Byphone CRUD ───────────────────────────
function viewNr(id) {
    apiFetch(RBYPHONE + '/' + id).then(d => {
        if (!d.success) return;
        const n = d.data;
        document.getElementById('nv-nama').textContent    = n.nama;
        document.getElementById('nv-instansi').textContent = n.instansi;
        document.getElementById('nv-nomor').innerHTML      = waLinkHTML(n.nomor_telp);
        document.getElementById('nv-tema').textContent    = n.tema_wawancara;
        document.getElementById('nv-waktu').textContent   = formatDateTime(n.waktu_wawancara);
        document.getElementById('nv-draft').textContent   = n.draft_pertanyaan || '(kosong)';
        openModal('m-nr-view');
    });
}
function editNr(id) {
    apiFetch(RBYPHONE + '/' + id).then(d => {
        if (!d.success) return;
        const n = d.data;
        document.getElementById('ne-id').value       = n.byphoneID;
        document.getElementById('ne-nama').value     = n.nama;
        document.getElementById('ne-instansi').value = n.instansi;
        document.getElementById('ne-nomor').value    = n.nomor_telp;
        document.getElementById('ne-tema').value     = n.tema_wawancara;
        const wVal = n.waktu_wawancara ? n.waktu_wawancara.replace(' ', 'T').substring(0, 16) : '';
        document.getElementById('ne-waktu').value    = wVal;
        syncFormDatetime('ne-waktu-lbl', 'ne-waktu-wrap', wVal);
        document.getElementById('ne-jadwal').value   = n.byphone_schedule || '';
        document.getElementById('ne-draft').value    = n.draft_pertanyaan || '';
        openModal('m-nr-edit');
    });
}
function submitNrAdd(e) {
    e.preventDefault();
    apiFetch(RBYPHONE, 'POST', {
        nama:            document.getElementById('na-nama').value,
        instansi:        document.getElementById('na-instansi').value,
        nomor_telp:      document.getElementById('na-nomor').value,
        tema_wawancara:  document.getElementById('na-tema').value,
        waktu_wawancara: document.getElementById('na-waktu').value.replace('T', ' '),
        draft_pertanyaan: document.getElementById('na-draft').value,
    }).then(d => {
        if (d.success) { closeModal('m-nr-add'); document.getElementById('na-nama').closest('form').reset(); showFlash(d.message); doSearchNr(nrCurrentPage); updateStats(); }
        else showFlash(d.message || 'Gagal menyimpan.', 'error');
    });
}
function submitNrEdit(e) {
    e.preventDefault();
    const id = document.getElementById('ne-id').value;
    apiFetch(RBYPHONE + '/' + id, 'PUT', {
        nama:            document.getElementById('ne-nama').value,
        instansi:        document.getElementById('ne-instansi').value,
        nomor_telp:      document.getElementById('ne-nomor').value,
        tema_wawancara:  document.getElementById('ne-tema').value,
        waktu_wawancara: document.getElementById('ne-waktu').value.replace('T', ' '),
        byphone_schedule: document.getElementById('ne-jadwal').value || null,
        draft_pertanyaan: document.getElementById('ne-draft').value,
    }).then(d => {
        if (d.success) { closeModal('m-nr-edit'); showFlash(d.message); doSearchNr(nrCurrentPage); updateStats(); }
        else showFlash(d.message || 'Gagal memperbarui.', 'error');
    });
}
function deleteNr(id) {
    showDeleteConfirm(
        'Hapus Data Narasumber',
        'Apakah Anda yakin ingin menghapus data narasumber ini? Tindakan ini tidak dapat dibatalkan.',
        function(done) {
            apiFetch(RBYPHONE + '/' + id, 'DELETE').then(d => {
                done();
                if (d.success) { showFlash(d.message); doSearchNr(nrCurrentPage); updateStats(); }
                else showFlash(d.message || 'Gagal menghapus.', 'error');
            }).catch(function() { done(); showFlash('Terjadi kesalahan, coba lagi.', 'error'); });
        }
    );
}

// ── Date helpers ──────────────────────────────────────
const BULAN_ID = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
function formatDate(d) {
    if (!d) return '—';
    const dt = new Date(d);
    return dt.getDate() + '/' + BULAN_ID[dt.getMonth()] + '/' + dt.getFullYear();
}
function formatDateTime(d) {
    if (!d) return '—';
    const dt = new Date(d.replace(' ', 'T'));
    const tgl = dt.getDate() + '/' + BULAN_ID[dt.getMonth()] + '/' + dt.getFullYear();
    const jam = String(dt.getHours()).padStart(2,'0') + ':' + String(dt.getMinutes()).padStart(2,'0');
    return tgl + ' ' + jam;
}

// ── Export Excel ──────────────────────────────────────
const REXPORT_BYPHONE = '{{ route('gatekeeper.export-byphone') }}';
const REXPORT_SIARAN  = '{{ route('gatekeeper.export-siaran') }}';

function doExportByphone(period) {
    document.getElementById('expbp-loading').style.display = 'block';
    ['expbp-week','expbp-month','expbp-year'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.pointerEvents = 'none';
    });
    window.location.href = REXPORT_BYPHONE + '?period=' + period;
    setTimeout(function() {
        document.getElementById('expbp-loading').style.display = 'none';
        ['expbp-week','expbp-month','expbp-year'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.pointerEvents = '';
        });
    }, 2000);
    return false;
}

function doExportSiaran(period) {
    document.getElementById('expsr-loading').style.display = 'block';
    ['expsr-week','expsr-month','expsr-year'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.pointerEvents = 'none';
    });
    window.location.href = REXPORT_SIARAN + '?period=' + period;
    setTimeout(function() {
        document.getElementById('expsr-loading').style.display = 'none';
        ['expsr-week','expsr-month','expsr-year'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.pointerEvents = '';
        });
    }, 2000);
    return false;
}

document.addEventListener('DOMContentLoaded', () => {
    doSearchSiaran(1);
    doSearchNr(1);
});

// ── Form date input helpers ─────────────────────────
function syncFormDate(lblId, wrapId, val) {
    const lbl  = document.getElementById(lblId);
    const wrap = document.getElementById(wrapId);
    if (val) {
        const dt = new Date(val + 'T00:00:00');
        lbl.textContent = dt.getDate() + '/' + BULAN_FILTER[dt.getMonth()] + '/' + dt.getFullYear();
        lbl.classList.remove('ph');
        if (wrap) wrap.classList.add('has-value');
    } else {
        lbl.textContent = 'Pilih tanggal…';
        lbl.classList.add('ph');
        if (wrap) wrap.classList.remove('has-value');
    }
}
function syncFormDatetime(lblId, wrapId, val) {
    const lbl  = document.getElementById(lblId);
    const wrap = document.getElementById(wrapId);
    if (val) {
        const dt  = new Date(val);
        const tgl = dt.getDate() + '/' + BULAN_FILTER[dt.getMonth()] + '/' + dt.getFullYear();
        const jam = String(dt.getHours()).padStart(2,'0') + ':' + String(dt.getMinutes()).padStart(2,'0');
        lbl.textContent = tgl + ' ' + jam;
        lbl.classList.remove('ph');
        if (wrap) wrap.classList.add('has-value');
    } else {
        lbl.textContent = 'Pilih tanggal & waktu…';
        lbl.classList.add('ph');
        if (wrap) wrap.classList.remove('has-value');
    }
}
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
</script>
@endpush
