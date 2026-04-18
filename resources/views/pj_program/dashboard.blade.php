@extends('layouts.pjprogram')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard PJ Program')
@section('page-sub', 'Manajemen narasumber talkshow')

@push('styles')
<style>
.wa-link { display:inline-flex; align-items:center; gap:4px; color:#16a34a; text-decoration:none; font-weight:500; transition:color .15s; }
.wa-link:hover { color:#15803d; text-decoration:underline; }
</style>
@endpush

@section('content')

{{-- ── Stats ── --}}
<div class="sg">
    <div class="sc">
        <div class="sc-ico" style="background:#fef3c7;">
            <svg width="16" height="16" viewBox="0 0 15 15" fill="none">
                <circle cx="7.5" cy="4.5" r="2.5" stroke="#92400e" stroke-width="1.3"/>
                <path d="M2 13c0-3 2.5-5 5.5-5s5.5 2 5.5 5" stroke="#92400e" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="sc-lbl">Total Narasumber</div>
        <div class="sc-val">{{ $stats['total_narasumber'] }}</div>
        <div class="sc-sub">Semua narasumber talkshow</div>
    </div>
    <div class="sc">
        <div class="sc-ico" style="background:#fce7f3;">
            <svg width="16" height="16" viewBox="0 0 15 15" fill="none">
                <circle cx="7.5" cy="7.5" r="5.5" stroke="#9d174d" stroke-width="1.3"/>
                <path d="M7.5 4.5v3l2 2" stroke="#9d174d" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="sc-lbl">Hari Ini</div>
        <div class="sc-val">{{ $stats['wawancara_hari_ini'] }}</div>
        <div class="sc-sub">Jadwal wawancara hari ini</div>
    </div>
    <div class="sc">
        <div class="sc-ico" style="background:#d1fae5;">
            <svg width="16" height="16" viewBox="0 0 15 15" fill="none">
                <rect x="2" y="2" width="11" height="11" rx="1.5" stroke="#065f46" stroke-width="1.3"/>
                <path d="M4.5 8l2 2 4-4" stroke="#065f46" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="sc-lbl">Mendatang (7 hari)</div>
        <div class="sc-val">{{ $stats['wawancara_mendatang'] }}</div>
        <div class="sc-sub">Wawancara dalam 7 hari ke depan</div>
    </div>
    <div class="sc">
        <div class="sc-ico" style="background:#e0e7ff;">
            <svg width="16" height="16" viewBox="0 0 15 15" fill="none">
                <circle cx="7.5" cy="7.5" r="5.5" stroke="#3730a3" stroke-width="1.3"/>
                <path d="M5 7.5h5" stroke="#3730a3" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="sc-lbl">Telah Selesai</div>
        <div class="sc-val">{{ $stats['wawancara_selesai'] }}</div>
        <div class="sc-sub">Wawancara sudah berlalu</div>
    </div>
</div>

{{-- ── Table ── --}}
<div class="tc">
    <div class="tbar">
        <div class="sw">
            <svg viewBox="0 0 15 15" fill="none">
                <circle cx="6.5" cy="6.5" r="4" stroke="currentColor" stroke-width="1.3"/>
                <path d="M11 11l2.5 2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            <input type="text" id="ts-search" placeholder="Cari nama, instansi, tema…" oninput="debounce(doSearch, 350)()">
        </div>
        <input type="date" class="fsel" id="ts-tanggal" onchange="doSearch()">
        <span class="rc" id="ts-count">{{ $narasumberList->count() }} data</span>
        <button class="btn-add" onclick="openModal('m-add')">
            <svg viewBox="0 0 15 15" fill="none">
                <path d="M7.5 1v13M1 7.5h13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Tambah Narasumber
        </button>
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
                    <th style="width:80px">Aksi</th>
                </tr>
            </thead>
            <tbody id="ts-tbody">
                @forelse($narasumberList as $i => $n)
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
                        <div class="abt">
                            <button class="bico" title="Detail" onclick="viewTs({{ $n->talkshowID }})">
                                <svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                            </button>
                            <button class="bico" title="Edit" onclick="editTs({{ $n->talkshowID }})">
                                <svg viewBox="0 0 15 15" fill="none"><path d="M10.5 2.5l2 2-8 8H2.5v-2l8-8z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>
                            </button>
                            <button class="bico del" title="Hapus" onclick="deleteTs({{ $n->talkshowID }})">
                                <svg viewBox="0 0 15 15" fill="none"><path d="M2.5 4.5h10M6 4.5V3h3v1.5M5.5 4.5v7h4v-7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="empty">Belum ada data narasumber talkshow.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pag" id="ts-pag">Menampilkan {{ $narasumberList->count() }} data</div>
</div>

{{-- ════════════════════ MODAL TAMBAH ════════════════════ --}}
<div class="mbg" id="m-add">
    <div class="modal">
        <div class="mttl">
            Tambah Narasumber Talkshow
            <button class="mclose" onclick="closeModal('m-add')">×</button>
        </div>
        <form onsubmit="submitAdd(event)">
            <div class="frow">
                <div class="fg">
                    <label class="fl">Nama</label>
                    <input type="text" class="fi" id="a-nama" placeholder="Nama lengkap" required>
                </div>
                <div class="fg">
                    <label class="fl">Instansi</label>
                    <input type="text" class="fi" id="a-instansi" placeholder="Nama instansi" required>
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label class="fl">Nomor Telepon</label>
                    <input type="text" class="fi" id="a-nomor" placeholder="08xx" required>
                </div>
                <div class="fg">
                    <label class="fl">Waktu Wawancara</label>
                    <input type="datetime-local" class="fi" id="a-waktu" required>
                </div>
            </div>
            <div class="fg">
                <label class="fl">Tema Wawancara</label>
                <input type="text" class="fi" id="a-tema" placeholder="Topik wawancara" required>
            </div>
            <div class="fg">
                <label class="fl">Draft Pertanyaan <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
                <textarea class="fi" id="a-draft" rows="3" placeholder="Daftar pertanyaan…"></textarea>
            </div>
            <div class="mft">
                <button type="button" class="bcn" onclick="closeModal('m-add')">Batal</button>
                <button type="submit" class="bsv">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════ MODAL EDIT ════════════════════ --}}
<div class="mbg" id="m-edit">
    <div class="modal">
        <div class="mttl">
            Edit Narasumber Talkshow
            <button class="mclose" onclick="closeModal('m-edit')">×</button>
        </div>
        <form onsubmit="submitEdit(event)">
            <input type="hidden" id="e-id">
            <div class="frow">
                <div class="fg">
                    <label class="fl">Nama</label>
                    <input type="text" class="fi" id="e-nama" required>
                </div>
                <div class="fg">
                    <label class="fl">Instansi</label>
                    <input type="text" class="fi" id="e-instansi" required>
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label class="fl">Nomor Telepon</label>
                    <input type="text" class="fi" id="e-nomor" required>
                </div>
                <div class="fg">
                    <label class="fl">Waktu Wawancara</label>
                    <input type="datetime-local" class="fi" id="e-waktu" required>
                </div>
            </div>
            <div class="fg">
                <label class="fl">Tema Wawancara</label>
                <input type="text" class="fi" id="e-tema" required>
            </div>
            <div class="fg">
                <label class="fl">Draft Pertanyaan <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
                <textarea class="fi" id="e-draft" rows="3"></textarea>
            </div>
            <div class="mft">
                <button type="button" class="bcn" onclick="closeModal('m-edit')">Batal</button>
                <button type="submit" class="bsv">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════ MODAL DETAIL ════════════════════ --}}
<div class="mbg" id="m-view">
    <div class="modal">
        <div class="mttl">
            Detail Narasumber Talkshow
            <button class="mclose" onclick="closeModal('m-view')">×</button>
        </div>
        <div class="dr"><span class="dl">Nama</span><span class="dv" id="v-nama">—</span></div>
        <div class="dr"><span class="dl">Instansi</span><span class="dv" id="v-instansi">—</span></div>
        <div class="dr"><span class="dl">Nomor Telepon</span><span class="dv" id="v-nomor">—</span></div>
        <div class="dr"><span class="dl">Tema Wawancara</span><span class="dv" id="v-tema">—</span></div>
        <div class="dr"><span class="dl">Waktu Wawancara</span><span class="dv" id="v-waktu">—</span></div>
        <div class="dr" style="flex-direction:column;">
            <span class="dl">Draft Pertanyaan</span>
            <div class="dbox" id="v-draft">—</div>
        </div>
        <div class="mft"><button class="bcn" onclick="closeModal('m-view')">Tutup</button></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const RTALKSHOW = '{{ url('pj-program/narasumber-talkshow') }}';
const RTSEARCH  = '{{ route('pj_program.talkshow.search') }}';

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

function doSearch() {
    const kw = document.getElementById('ts-search').value;
    const tg = document.getElementById('ts-tanggal').value;
    const url = RTSEARCH + '?keyword=' + encodeURIComponent(kw) + (tg ? '&tanggal=' + tg : '');
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
                    <td><div class="abt">
                        <button class="bico" onclick="viewTs(${n.talkshowID})"><svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg></button>
                        <button class="bico" onclick="editTs(${n.talkshowID})"><svg viewBox="0 0 15 15" fill="none"><path d="M10.5 2.5l2 2-8 8H2.5v-2l8-8z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg></button>
                        <button class="bico del" onclick="deleteTs(${n.talkshowID})"><svg viewBox="0 0 15 15" fill="none"><path d="M2.5 4.5h10M6 4.5V3h3v1.5M5.5 4.5v7h4v-7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                    </div></td>
                </tr>`).join('');
            document.getElementById('ts-tbody').innerHTML = rows || '<tr><td colspan="7" class="empty">Tidak ada data.</td></tr>';
            document.getElementById('ts-count').textContent = d.total + ' data';
            document.getElementById('ts-pag').textContent = 'Menampilkan ' + d.total + ' data';
        });
}

function viewTs(id) {
    apiFetch(RTALKSHOW + '/' + id).then(d => {
        if (!d.success) return;
        const n = d.data;
        document.getElementById('v-nama').textContent     = n.nama;
        document.getElementById('v-instansi').textContent = n.instansi;
        document.getElementById('v-nomor').innerHTML      = waLinkHTML(n.nomor_telp);
        document.getElementById('v-tema').textContent     = n.tema_wawancara;
        document.getElementById('v-waktu').textContent    = formatDateTime(n.waktu_wawancara);
        document.getElementById('v-draft').textContent    = n.draft_pertanyaan || '(kosong)';
        openModal('m-view');
    });
}
function editTs(id) {
    apiFetch(RTALKSHOW + '/' + id).then(d => {
        if (!d.success) return;
        const n = d.data;
        document.getElementById('e-id').value       = n.talkshowID;
        document.getElementById('e-nama').value     = n.nama;
        document.getElementById('e-instansi').value = n.instansi;
        document.getElementById('e-nomor').value    = n.nomor_telp;
        document.getElementById('e-tema').value     = n.tema_wawancara;
        document.getElementById('e-waktu').value    = n.waktu_wawancara ? n.waktu_wawancara.replace(' ', 'T').substring(0, 16) : '';
        document.getElementById('e-draft').value    = n.draft_pertanyaan || '';
        openModal('m-edit');
    });
}
function submitAdd(e) {
    e.preventDefault();
    apiFetch(RTALKSHOW, 'POST', {
        nama:             document.getElementById('a-nama').value,
        instansi:         document.getElementById('a-instansi').value,
        nomor_telp:       document.getElementById('a-nomor').value,
        tema_wawancara:   document.getElementById('a-tema').value,
        waktu_wawancara:  document.getElementById('a-waktu').value.replace('T', ' '),
        draft_pertanyaan: document.getElementById('a-draft').value,
    }).then(d => {
        if (d.success) { closeModal('m-add'); document.getElementById('a-nama').closest('form').reset(); showFlash(d.message); doSearch(); }
        else showFlash(d.message || 'Gagal menyimpan.', 'error');
    });
}
function submitEdit(e) {
    e.preventDefault();
    const id = document.getElementById('e-id').value;
    apiFetch(RTALKSHOW + '/' + id, 'PUT', {
        nama:             document.getElementById('e-nama').value,
        instansi:         document.getElementById('e-instansi').value,
        nomor_telp:       document.getElementById('e-nomor').value,
        tema_wawancara:   document.getElementById('e-tema').value,
        waktu_wawancara:  document.getElementById('e-waktu').value.replace('T', ' '),
        draft_pertanyaan: document.getElementById('e-draft').value,
    }).then(d => {
        if (d.success) { closeModal('m-edit'); showFlash(d.message); doSearch(); }
        else showFlash(d.message || 'Gagal memperbarui.', 'error');
    });
}
function deleteTs(id) {
    showDeleteConfirm(
        'Hapus Data Narasumber Talkshow',
        'Apakah Anda yakin ingin menghapus data narasumber talkshow ini? Tindakan ini tidak dapat dibatalkan.',
        function(done) {
            apiFetch(RTALKSHOW + '/' + id, 'DELETE').then(d => {
                done();
                if (d.success) { showFlash(d.message); doSearch(); }
                else showFlash(d.message || 'Gagal menghapus.', 'error');
            }).catch(function() { done(); showFlash('Terjadi kesalahan, coba lagi.', 'error'); });
        }
    );
}

function formatDateTime(d) {
    if (!d) return '—';
    const dt = new Date(d.replace(' ', 'T'));
    return dt.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' })
        + ' ' + dt.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
}
</script>
@endpush
