@extends('layouts.pjprogram')

@section('title', 'Dashboard')
@section('page-title', '')
@section('page-sub', 'Manajemen narasumber talkshow')

@push('styles')
<style>
.wa-link { display:inline-flex; align-items:center; gap:4px; color:#16a34a; text-decoration:none; font-weight:500; transition:color .15s; }
.wa-link:hover { color:#15803d; text-decoration:underline; }
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
/* ── Jadwal Talkshow dropdown ── */
.sched-select {
    font-size: 11px; padding: 4px 6px; border-radius: 6px;
    border: 1px solid #cbd5e1; background: #f8fafc; color: #0f172a;
    cursor: pointer; min-width: 140px; max-width: 180px;
    transition: border-color .15s, background .15s;
}
.sched-select:focus { outline: none; border-color: #0f766e; background: #fff; }
.sched-select.saving { opacity: .6; pointer-events: none; }
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
            <div class="sb-sc-ico" style="background:#fef3c7;"><svg viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="4.5" r="2.5" stroke="#92400e" stroke-width="1.3"/><path d="M2 13c0-3 2.5-5 5.5-5s5.5 2 5.5 5" stroke="#92400e" stroke-width="1.3" stroke-linecap="round"/></svg></div>
            <div class="sb-sc-val">{{ $stats['total_narasumber'] }}</div>
            <div class="sb-sc-lbl">Total</div>
        </div>
        <div class="sb-sc">
            <div class="sb-sc-ico" style="background:#fce7f3;"><svg viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="5.5" stroke="#9d174d" stroke-width="1.3"/><path d="M7.5 4.5v3l2 2" stroke="#9d174d" stroke-width="1.3" stroke-linecap="round"/></svg></div>
            <div class="sb-sc-val">{{ $stats['wawancara_hari_ini'] }}</div>
            <div class="sb-sc-lbl">Hari Ini</div>
        </div>
        <div class="sb-sc">
            <div class="sb-sc-ico" style="background:#d1fae5;"><svg viewBox="0 0 15 15" fill="none"><rect x="2" y="2" width="11" height="11" rx="1.5" stroke="#065f46" stroke-width="1.3"/><path d="M4.5 8l2 2 4-4" stroke="#065f46" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            <div class="sb-sc-val">{{ $stats['wawancara_mendatang'] }}</div>
            <div class="sb-sc-lbl">Mendatang</div>
        </div>
        <div class="sb-sc">
            <div class="sb-sc-ico" style="background:#e0e7ff;"><svg viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="5.5" stroke="#3730a3" stroke-width="1.3"/><path d="M5 7.5h5" stroke="#3730a3" stroke-width="1.3" stroke-linecap="round"/></svg></div>
            <div class="sb-sc-val">{{ $stats['wawancara_selesai'] }}</div>
            <div class="sb-sc-lbl">Selesai</div>
        </div>
    </div>
</div>
@endsection

@section('content')

{{-- ── Table ── --}}
<div class="tc">
    <div class="tbar">
        <div class="sw">
            <svg viewBox="0 0 15 15" fill="none">
                <circle cx="6.5" cy="6.5" r="4" stroke="currentColor" stroke-width="1.3"/>
                <path d="M11 11l2.5 2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            <input type="text" id="ts-search" placeholder="Cari nama, instansi, tema, jadwal…" oninput="debounce(doSearch, 350)()">
        </div>
        <span style="font-size:12px;color:#64748b;white-space:nowrap;">Filter tanggal:</span>
        <div class="date-filter-wrap" id="ts-tanggal-wrap" onclick="triggerDatePicker('ts-tanggal')">
            <svg width="13" height="13" viewBox="0 0 15 15" fill="none"><rect x="1.5" y="2.5" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M10 1v3M1.5 6h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
            <span id="ts-tanggal-lbl">Semua tanggal</span>
            <button type="button" class="date-clear-btn" style="display:none" onclick="event.stopPropagation();clearDateFilter('ts-tanggal','ts-tanggal-lbl','ts-tanggal-wrap',doSearch)">×</button>
            <input type="date" id="ts-tanggal" onchange="syncDateDisplay('ts-tanggal-lbl','ts-tanggal-wrap',this.value);doSearch()">
        </div>
        <select class="fsel" id="ts-limit" onchange="doSearch()">
            <option value="10">10</option>
            <option value="15" selected>15</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <span class="rc" id="ts-count">{{ $narasumberList->count() }} data</span>
        <button class="btn-add" onclick="openModal('m-add')">
            <svg viewBox="0 0 15 15" fill="none">
                <path d="M7.5 1v13M1 7.5h13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Tambah Narasumber
        </button>
        <button class="btn-add b-green" onclick="openModal('m-export')">
            <svg width="12" height="12" viewBox="0 0 15 15" fill="none">
                <path d="M7.5 1v9M4.5 7l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 11v1.5a.5.5 0 00.5.5h10a.5.5 0 00.5-.5V11" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            Export Talkshow
        </button>
    </div>

    <div style="font-weight:600;color:#64748b;font-size:13px;padding:0 8px 10px;">
        Tabel Narasumber Talkshow
    </div>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th style="width:60px">Nomor</th>
                    <th>Nama</th>
                    <th>Instansi</th>
                    <th style="width:120px">Nomor Telepon</th>
                    <th>Tema Wawancara</th>
                    <th style="width:115px">Waktu</th>
                    <th style="width:185px">Jenis Jadwal Talkshow</th>
                    <th style="width:80px">Aksi</th>
                </tr>
            </thead>
            <tbody id="ts-tbody">
                @forelse($narasumberList as $i => $n)
                <tr>
                    <td class="mono">{{ $narasumberList->firstItem() + $i }}</td>
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
                <tr><td colspan="8" class="empty">Belum ada data narasumber talkshow.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pag" id="ts-pag"></div>
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
                    <div class="date-input-wrap" id="a-waktu-wrap" onclick="triggerDatePicker('a-waktu')">
                        <svg width="14" height="14" viewBox="0 0 15 15" fill="none"><rect x="1.5" y="2.5" width="12" height="11" rx="1.5" stroke="#94a3b8" stroke-width="1.3"/><path d="M5 1v3M10 1v3M1.5 6h12" stroke="#94a3b8" stroke-width="1.3" stroke-linecap="round"/></svg>
                        <span class="date-disp ph" id="a-waktu-lbl">Pilih tanggal &amp; waktu…</span>
                        <input type="datetime-local" id="a-waktu" onchange="syncFormDatetime('a-waktu-lbl','a-waktu-wrap',this.value)">
                    </div>
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
                    <div class="date-input-wrap" id="e-waktu-wrap" onclick="triggerDatePicker('e-waktu')">
                        <svg width="14" height="14" viewBox="0 0 15 15" fill="none"><rect x="1.5" y="2.5" width="12" height="11" rx="1.5" stroke="#94a3b8" stroke-width="1.3"/><path d="M5 1v3M10 1v3M1.5 6h12" stroke="#94a3b8" stroke-width="1.3" stroke-linecap="round"/></svg>
                        <span class="date-disp ph" id="e-waktu-lbl">Pilih tanggal &amp; waktu…</span>
                        <input type="datetime-local" id="e-waktu" onchange="syncFormDatetime('e-waktu-lbl','e-waktu-wrap',this.value)">
                    </div>
                </div>
            </div>
            <div class="fg">
                <label class="fl">Tema Wawancara</label>
                <input type="text" class="fi" id="e-tema" required>
            </div>
            <div class="fg">
                <label class="fl">Jenis Jadwal Talkshow <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
                <select class="fi" id="e-jadwal">
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

{{-- ════════════════════ MODAL EXPORT EXCEL ════════════════════ --}}
<div class="mbg" id="m-export">
    <div class="modal" style="max-width:360px;">
        <div class="mttl">
            Export Narasumber Talkshow
            <button class="mclose" onclick="closeModal('m-export')">×</button>
        </div>
        <p style="font-size:13px;color:#64748b;margin:0 0 16px;">
            Pilih periode data yang ingin di-export. File Excel akan berisi sheet: <strong>Narasumber Talkshow</strong>.
        </p>
        <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px;">
            <a id="exp-week" href="#" class="exp-opt" onclick="doExport('week')">
                <div class="exp-ico">📅</div>
                <div>
                    <div class="exp-lbl">Minggu Ini</div>
                    <div class="exp-sub">Data 7 hari terakhir</div>
                </div>
            </a>
            <a id="exp-month" href="#" class="exp-opt" onclick="doExport('month')">
                <div class="exp-ico">🗓️</div>
                <div>
                    <div class="exp-lbl">Bulan Ini</div>
                    <div class="exp-sub">Data 30 hari terakhir</div>
                </div>
            </a>
            <a id="exp-year" href="#" class="exp-opt" onclick="doExport('year')">
                <div class="exp-ico">📆</div>
                <div>
                    <div class="exp-lbl">Tahun Ini</div>
                    <div class="exp-sub">Data 365 hari terakhir</div>
                </div>
            </a>
        </div>
        <div id="exp-loading" style="display:none;text-align:center;font-size:13px;color:#64748b;padding:8px 0;">
            ⏳ Sedang memproses export…
        </div>
        <div class="mft">
            <button class="bcn" onclick="closeModal('m-export')">Batal</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const RTALKSHOW = '{{ url('pj-program/narasumber-talkshow') }}';
const RTSEARCH  = '{{ route('pj_program.talkshow.search') }}';
const RSCHEDULE = '{{ url('pj-program/narasumber-talkshow') }}';

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

let tsCurrentPage = 1;
function doSearch(page = null) {
    if (page !== null) tsCurrentPage = page;
    else tsCurrentPage = 1; // reset on search

    const kw = document.getElementById('ts-search').value;
    const tg = document.getElementById('ts-tanggal').value;
    const lim = document.getElementById('ts-limit').value;
    const url = RTSEARCH + '?page=' + tsCurrentPage + '&keyword=' + encodeURIComponent(kw) + (tg ? '&tanggal=' + tg : '') + '&limit=' + lim;
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
                    <td><div class="abt">
                        <button class="bico" onclick="viewTs(${n.talkshowID})"><svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg></button>
                        <button class="bico" onclick="editTs(${n.talkshowID})"><svg viewBox="0 0 15 15" fill="none"><path d="M10.5 2.5l2 2-8 8H2.5v-2l8-8z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg></button>
                        <button class="bico del" onclick="deleteTs(${n.talkshowID})"><svg viewBox="0 0 15 15" fill="none"><path d="M2.5 4.5h10M6 4.5V3h3v1.5M5.5 4.5v7h4v-7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                    </div></td>
                </tr>`).join('');
            document.getElementById('ts-tbody').innerHTML = rows || '<tr><td colspan="8" class="empty">Tidak ada data.</td></tr>';
            document.getElementById('ts-count').textContent = d.total + ' data';
            document.getElementById('ts-pag').innerHTML = renderPagination(d, 'doSearch');
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
        const wVal = n.waktu_wawancara ? n.waktu_wawancara.replace(' ', 'T').substring(0, 16) : '';
        document.getElementById('e-waktu').value    = wVal;
        syncFormDatetime('e-waktu-lbl', 'e-waktu-wrap', wVal);
        document.getElementById('e-jadwal').value   = n.talkshow_schedule || '';
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
        if (d.success) { closeModal('m-add'); document.getElementById('a-nama').closest('form').reset(); showFlash(d.message); doSearch(tsCurrentPage); }
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
        talkshow_schedule: document.getElementById('e-jadwal').value || null,
        draft_pertanyaan: document.getElementById('e-draft').value,
    }).then(d => {
        if (d.success) { closeModal('m-edit'); showFlash(d.message); doSearch(tsCurrentPage); }
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
                if (d.success) { showFlash(d.message); doSearch(tsCurrentPage); }
                else showFlash(d.message || 'Gagal menghapus.', 'error');
            }).catch(function() { done(); showFlash('Terjadi kesalahan, coba lagi.', 'error'); });
        }
    );
}

const BULAN_ID = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
function formatDateTime(d) {
    if (!d) return '—';
    const dt = new Date(d.replace(' ', 'T'));
    const tgl = dt.getDate() + '/' + BULAN_ID[dt.getMonth()] + '/' + dt.getFullYear();
    const jam = String(dt.getHours()).padStart(2,'0') + ':' + String(dt.getMinutes()).padStart(2,'0');
    return tgl + ' ' + jam;
}

// ── Export Excel ──────────────────────────────────────
const REXPORT = '{{ route('pj_program.export-talkshow') }}';
function doExport(period) {
    document.getElementById('exp-loading').style.display = 'block';
    ['exp-week','exp-month','exp-year'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.pointerEvents = 'none';
    });
    window.location.href = REXPORT + '?period=' + period;
    setTimeout(function() {
        document.getElementById('exp-loading').style.display = 'none';
        ['exp-week','exp-month','exp-year'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.pointerEvents = '';
        });
    }, 2000);
    return false;
}

document.addEventListener('DOMContentLoaded', () => {
    doSearch(1);
});

// ── Form date input helpers ─────────────────────────
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
</script>
@endpush
