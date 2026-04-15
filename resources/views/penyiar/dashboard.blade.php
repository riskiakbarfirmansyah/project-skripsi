@extends('layouts.penyiar')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Penyiar')
@section('page-sub', 'Data narasumber byphone & talkshow')

@section('content')

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
                        <td class="mono">{{ $n->nomor_telp }}</td>
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
                        <td class="mono">{{ $n->nomor_telp }}</td>
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
                    <td class="mono">${n.nomor_telp}</td>
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
                    <td class="mono">${n.nomor_telp}</td>
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
        document.getElementById('bv-nomor').textContent    = n.nomor_telp;
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
        document.getElementById('tv-nomor').textContent    = n.nomor_telp;
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
</script>
@endpush
