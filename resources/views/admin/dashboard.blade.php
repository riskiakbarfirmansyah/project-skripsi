@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Admin')
@section('page-sub', 'Manajemen pengguna sistem radio')

@section('content')

{{-- ── Stats ── --}}
<div class="sg">
    <div class="sc">
        <div class="sc-ico" style="background:#f5f3ff;">
            <svg width="16" height="16" viewBox="0 0 15 15" fill="none">
                <circle cx="7.5" cy="4.5" r="2.5" stroke="#7c3aed" stroke-width="1.3"/>
                <path d="M2 13c0-3 2.5-5 5.5-5s5.5 2 5.5 5" stroke="#7c3aed" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="sc-lbl">Total Pengguna</div>
        <div class="sc-val" id="stat-total">{{ $stats['total_user'] }}</div>
        <div class="sc-sub">Gatekeeper + Penyiar + PJ</div>
    </div>
    <div class="sc">
        <div class="sc-ico" style="background:#dbeafe;">
            <svg width="16" height="16" viewBox="0 0 15 15" fill="none">
                <path d="M7.5 1L2 3.5v4c0 3 2.5 5.5 5.5 6 3-.5 5.5-3 5.5-6v-4L7.5 1z"
                      stroke="#1e40af" stroke-width="1.3" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="sc-lbl">Gatekeeper</div>
        <div class="sc-val" id="stat-gk">{{ $stats['total_gatekeeper'] }}</div>
        <div class="sc-sub">Pengelola siaran</div>
    </div>
    <div class="sc">
        <div class="sc-ico" style="background:#d1fae5;">
            <svg width="16" height="16" viewBox="0 0 15 15" fill="none">
                <circle cx="7.5" cy="7.5" r="2.5" stroke="#065f46" stroke-width="1.3"/>
                <circle cx="7.5" cy="7.5" r="6" stroke="#065f46" stroke-width="1.3"/>
            </svg>
        </div>
        <div class="sc-lbl">Penyiar</div>
        <div class="sc-val" id="stat-py">{{ $stats['total_penyiar'] }}</div>
        <div class="sc-sub">On-air broadcaster</div>
    </div>
    <div class="sc">
        <div class="sc-ico" style="background:#fef3c7;">
            <svg width="16" height="16" viewBox="0 0 15 15" fill="none">
                <rect x="2" y="2" width="11" height="11" rx="1.5" stroke="#92400e" stroke-width="1.3"/>
                <path d="M5 7.5h5M7.5 5v5" stroke="#92400e" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="sc-lbl">PJ Program</div>
        <div class="sc-val" id="stat-pj">{{ $stats['total_pj_program'] }}</div>
        <div class="sc-sub">Penanggungjawab program</div>
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
            <input type="text" id="search-input" placeholder="Cari nama atau email…" oninput="debounce(doSearch, 350)()">
        </div>
        <select class="fsel" id="role-filter" onchange="doSearch()">
            <option value="">Semua Role</option>
            <option value="gatekeeper">Gatekeeper</option>
            <option value="penyiar">Penyiar</option>
            <option value="pj_program">PJ Program</option>
        </select>
        <span class="rc" id="result-count">{{ $users->count() }} pengguna</span>
        <button class="btn-add" onclick="openModal('m-add')">
            <svg viewBox="0 0 15 15" fill="none">
                <path d="M7.5 1v13M1 7.5h13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Tambah Pengguna
        </button>
    </div>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th style="width:110px">Role</th>
                    <th style="width:80px">Aksi</th>
                </tr>
            </thead>
            <tbody id="user-tbody">
                @forelse($users as $i => $u)
                <tr data-id="{{ $u->id }}">
                    <td class="mono">{{ $i + 1 }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:9px;">
                            <div class="ini">{{ strtoupper(substr($u->name, 0, 2)) }}</div>
                            <div>
                                <div style="font-weight:600;font-size:13px;">{{ $u->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="trunc" style="max-width:200px;font-size:12px;color:#64748b;">{{ $u->email }}</td>
                    <td>
                        <span class="role-badge role-{{ $u->role }}">{{ ucfirst(str_replace('_', ' ', $u->role)) }}</span>
                    </td>
                    <td>
                        <div class="abt">
                            <button class="bico" title="Detail" onclick="viewUser({{ $u->id }})">
                                <svg viewBox="0 0 15 15" fill="none">
                                    <path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/>
                                    <circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/>
                                </svg>
                            </button>
                            <button class="bico" title="Edit" onclick="editUser({{ $u->id }})">
                                <svg viewBox="0 0 15 15" fill="none">
                                    <path d="M10.5 2.5l2 2-8 8H2.5v-2l8-8z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button class="bico" title="Reset Password"
                                    data-id="{{ $u->id }}" data-name="{{ $u->name }}"
                                    onclick="resetPasswordUser(this.dataset.id, this.dataset.name)">
                                <svg viewBox="0 0 15 15" fill="none">
                                    <path d="M2 7.5C2 4.5 4.5 2 7.5 2c2 0 3.7 1 4.7 2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                                    <path d="M13 7.5c0 3-2.5 5.5-5.5 5.5-2 0-3.7-1-4.7-2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                                    <path d="M10.5 2l2 2.5-2.5.5M4.5 13l-2-2.5 2.5-.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button class="bico del" title="Hapus"
                                    data-id="{{ $u->id }}" data-name="{{ $u->name }}"
                                    onclick="deleteUser(this.dataset.id, this.dataset.name)">
                                <svg viewBox="0 0 15 15" fill="none">
                                    <path d="M2.5 4.5h10M6 4.5V3h3v1.5M5.5 4.5v7h4v-7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty">Belum ada pengguna terdaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pag" id="pag-info">Menampilkan {{ $users->count() }} pengguna</div>
</div>

{{-- ════════════════════ MODAL TAMBAH ════════════════════ --}}
<div class="mbg" id="m-add">
    <div class="modal">
        <div class="mttl">
            Tambah Pengguna Baru
            <button class="mclose" onclick="closeModal('m-add')">×</button>
        </div>
        <form id="form-add" onsubmit="submitAdd(event)">
            <div class="fg">
                <label class="fl">Nama Lengkap</label>
                <input type="text" class="fi" id="add-name" placeholder="Nama lengkap" required>
                <div class="err-txt" id="err-add-name"></div>
            </div>
            <div class="fg">
                <label class="fl">Email</label>
                <input type="email" class="fi" id="add-email" placeholder="email@example.com" required>
                <div class="err-txt" id="err-add-email"></div>
            </div>
            <div class="fg">
                <label class="fl">Role</label>
                <select class="fi" id="add-role" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="gatekeeper">Gatekeeper</option>
                    <option value="penyiar">Penyiar</option>
                    <option value="pj_program">PJ Program</option>
                </select>
                <div class="err-txt" id="err-add-role"></div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label class="fl">Password</label>
                    <input type="password" class="fi" id="add-password" placeholder="Min. 8 karakter" required>
                    <div class="err-txt" id="err-add-password"></div>
                </div>
                <div class="fg">
                    <label class="fl">Konfirmasi Password</label>
                    <input type="password" class="fi" id="add-password-confirmation" placeholder="Ulangi password" required>
                </div>
            </div>
            <div class="mft">
                <button type="button" class="bcn" onclick="closeModal('m-add')">Batal</button>
                <button type="submit" class="bsv" id="btn-add-save">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════ MODAL EDIT ════════════════════ --}}
<div class="mbg" id="m-edit">
    <div class="modal">
        <div class="mttl">
            Edit Pengguna
            <button class="mclose" onclick="closeModal('m-edit')">×</button>
        </div>
        <form id="form-edit" onsubmit="submitEdit(event)">
            <input type="hidden" id="edit-id">
            <div class="fg">
                <label class="fl">Nama Lengkap</label>
                <input type="text" class="fi" id="edit-name" placeholder="Nama lengkap" required>
                <div class="err-txt" id="err-edit-name"></div>
            </div>
            <div class="fg">
                <label class="fl">Email</label>
                <input type="email" class="fi" id="edit-email" placeholder="email@example.com" required>
                <div class="err-txt" id="err-edit-email"></div>
            </div>
            <div class="fg">
                <label class="fl">Role</label>
                <select class="fi" id="edit-role" required>
                    <option value="gatekeeper">Gatekeeper</option>
                    <option value="penyiar">Penyiar</option>
                    <option value="pj_program">PJ Program</option>
                </select>
            </div>
            <div class="mft">
                <button type="button" class="bcn" onclick="closeModal('m-edit')">Batal</button>
                <button type="submit" class="bsv" id="btn-edit-save">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════ MODAL DETAIL ════════════════════ --}}
<div class="mbg" id="m-view">
    <div class="modal">
        <div class="mttl">
            Detail Pengguna
            <button class="mclose" onclick="closeModal('m-view')">×</button>
        </div>
        <div id="view-body">
            {{-- Profile header --}}
            <div class="vd-profile">
                <div class="ini vd-ini" id="v-avatar">?</div>
                <div>
                    <div class="vd-pname" id="v-name">—</div>
                    <div class="vd-pemail" id="v-email">—</div>
                </div>
            </div>
            {{-- Detail fields --}}
            <div class="vd-fields">
                <div class="vd-row">
                    <span class="vd-label">Role</span>
                    <span id="v-role">—</span>
                </div>
                <div class="vd-row">
                    <span class="vd-label">Dibuat</span>
                    <span class="vd-val" id="v-created">—</span>
                </div>
                <div class="vd-row">
                    <span class="vd-label">Diperbarui</span>
                    <span class="vd-val" id="v-updated">—</span>
                </div>
            </div>
        </div>
        <div class="mft">
            <button type="button" class="bcn" onclick="closeModal('m-view')">Tutup</button>
        </div>
    </div>
</div>

{{-- ════════════════════ MODAL RESET PASSWORD ════════════════════ --}}
<div class="mbg" id="m-reset">
    <div class="modal">
        <div class="mttl">
            Reset Password
            <button class="mclose" onclick="closeModal('m-reset')">×</button>
        </div>
        <div style="font-size:13px;color:#475569;margin-bottom:16px;">
            Reset password untuk: <strong id="reset-name">—</strong>
        </div>
        <form id="form-reset" onsubmit="submitReset(event)">
            <input type="hidden" id="reset-id">
            <div class="fg">
                <label class="fl">Password Baru</label>
                <input type="password" class="fi" id="reset-password" placeholder="Min. 8 karakter" required>
                <div class="err-txt" id="err-reset-password"></div>
            </div>
            <div class="fg">
                <label class="fl">Konfirmasi Password Baru</label>
                <input type="password" class="fi" id="reset-password-confirmation" placeholder="Ulangi password" required>
            </div>
            <div class="mft">
                <button type="button" class="bcn" onclick="closeModal('m-reset')">Batal</button>
                <button type="submit" class="bsv bdel">Reset Password</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const ROUTES = {
    store:  '{{ route('admin.users.store') }}',
    show:   '{{ url('admin/users') }}',
    update: '{{ url('admin/users') }}',
    delete: '{{ url('admin/users') }}',
    search: '{{ route('admin.users.search') }}',
    reset:  '{{ url('admin/users') }}',
};

// ── Debounce ──────────────────────────────────────────
function debounce(fn, delay) {
    let t;
    return function(...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), delay); };
}

// ── Helpers ───────────────────────────────────────────
function roleBadge(role) {
    const map = { gatekeeper: 'role-gatekeeper', penyiar: 'role-penyiar', pj_program: 'role-pj_program' };
    const label = role === 'pj_program' ? 'PJ Program' : role.charAt(0).toUpperCase() + role.slice(1);
    return `<span class="role-badge ${map[role] || ''}">${label}</span>`;
}
function renderRows(users) {
    const tbody = document.getElementById('user-tbody');
    if (!users.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="empty">Tidak ada pengguna ditemukan.</td></tr>';
        return;
    }
    tbody.innerHTML = users.map((u, i) => `
        <tr data-id="${u.id}">
            <td class="mono">${i + 1}</td>
            <td>
                <div style="display:flex;align-items:center;gap:9px;">
                    <div class="ini">${ini(u.name)}</div>
                    <div style="font-weight:600;font-size:13px;">${u.name}</div>
                </div>
            </td>
            <td style="font-size:12px;color:#64748b;">${u.email}</td>
            <td>${roleBadge(u.role)}</td>
            <td>
                <div class="abt">
                    <button class="bico" title="Detail" onclick="viewUser(${u.id})">
                        <svg viewBox="0 0 15 15" fill="none"><path d="M1 7.5s2.5-5 6.5-5 6.5 5 6.5 5-2.5 5-6.5 5S1 7.5 1 7.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                    </button>
                    <button class="bico" title="Edit" onclick="editUser(${u.id})">
                        <svg viewBox="0 0 15 15" fill="none"><path d="M10.5 2.5l2 2-8 8H2.5v-2l8-8z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>
                    </button>
                    <button class="bico" title="Reset Password"
                            data-id="${u.id}" data-name="${u.name}"
                            onclick="resetPasswordUser(this.dataset.id, this.dataset.name)">
                        <svg viewBox="0 0 15 15" fill="none"><path d="M2 7.5C2 4.5 4.5 2 7.5 2c2 0 3.7 1 4.7 2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><path d="M13 7.5c0 3-2.5 5.5-5.5 5.5-2 0-3.7-1-4.7-2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><path d="M10.5 2l2 2.5-2.5.5M4.5 13l-2-2.5 2.5-.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <button class="bico del" title="Hapus"
                            data-id="${u.id}" data-name="${u.name}"
                            onclick="deleteUser(this.dataset.id, this.dataset.name)">
                        <svg viewBox="0 0 15 15" fill="none"><path d="M2.5 4.5h10M6 4.5V3h3v1.5M5.5 4.5v7h4v-7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </td>
        </tr>`).join('');
}

// ── Role filter from sidebar ───────────────────────────
function filterRole(role) {
    document.getElementById('role-filter').value = role;
    doSearch();
}

// ── Search ────────────────────────────────────────────
function doSearch() {
    const kw   = document.getElementById('search-input').value;
    const role = document.getElementById('role-filter').value;
    const url  = ROUTES.search + '?keyword=' + encodeURIComponent(kw) + '&role=' + encodeURIComponent(role);
    fetch(url, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                renderRows(d.data);
                document.getElementById('result-count').textContent = d.total + ' pengguna';
                document.getElementById('pag-info').textContent = 'Menampilkan ' + d.total + ' pengguna';
            }
        });
}

// ── View User ─────────────────────────────────────────
function viewUser(id) {
    apiFetch(ROUTES.show + '/' + id).then(d => {
        if (!d.success) return;
        const u = d.data;
        document.getElementById('v-avatar').textContent  = ini(u.name);
        document.getElementById('v-name').textContent    = u.name;
        document.getElementById('v-email').textContent   = u.email;
        document.getElementById('v-role').innerHTML      = roleBadge(u.role);
        document.getElementById('v-created').textContent = u.created_at;
        document.getElementById('v-updated').textContent = u.updated_at;
        openModal('m-view');
    });
}

// ── Edit User ─────────────────────────────────────────
function editUser(id) {
    apiFetch(ROUTES.show + '/' + id).then(d => {
        if (!d.success) return;
        const u = d.data;
        document.getElementById('edit-id').value    = u.id;
        document.getElementById('edit-name').value  = u.name;
        document.getElementById('edit-email').value = u.email;
        document.getElementById('edit-role').value  = u.role;
        ['name','email'].forEach(f => {
            const el = document.getElementById('err-edit-' + f);
            if (el) { el.style.display = 'none'; el.textContent = ''; }
        });
        openModal('m-edit');
    });
}

// ── Submit Add ────────────────────────────────────────
function submitAdd(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-add-save');
    btn.disabled = true; btn.textContent = 'Menyimpan…';
    const body = {
        name:                  document.getElementById('add-name').value,
        email:                 document.getElementById('add-email').value,
        role:                  document.getElementById('add-role').value,
        password:              document.getElementById('add-password').value,
        password_confirmation: document.getElementById('add-password-confirmation').value,
    };
    apiFetch(ROUTES.store, 'POST', body).then(d => {
        btn.disabled = false; btn.textContent = 'Simpan';
        if (d.success) {
            closeModal('m-add');
            document.getElementById('form-add').reset();
            showFlash(d.message);
            doSearch();
        } else {
            showErrors('add', d.errors || {});
        }
    }).catch(() => { btn.disabled = false; btn.textContent = 'Simpan'; showFlash('Terjadi kesalahan.', 'error'); });
}

// ── Submit Edit ───────────────────────────────────────
function submitEdit(e) {
    e.preventDefault();
    const id  = document.getElementById('edit-id').value;
    const btn = document.getElementById('btn-edit-save');
    btn.disabled = true; btn.textContent = 'Menyimpan…';
    const body = {
        name:  document.getElementById('edit-name').value,
        email: document.getElementById('edit-email').value,
        role:  document.getElementById('edit-role').value,
    };
    apiFetch(ROUTES.update + '/' + id, 'PUT', body).then(d => {
        btn.disabled = false; btn.textContent = 'Simpan Perubahan';
        if (d.success) {
            closeModal('m-edit');
            showFlash(d.message);
            doSearch();
        } else {
            showErrors('edit', d.errors || {});
        }
    }).catch(() => { btn.disabled = false; btn.textContent = 'Simpan Perubahan'; showFlash('Terjadi kesalahan.', 'error'); });
}

// ── Delete User ───────────────────────────────────────
function deleteUser(id, name) {
    showDeleteConfirm(
        'Hapus Akun Pengguna',
        'Apakah Anda yakin ingin menghapus akun "' + name + '"? Tindakan ini tidak dapat dibatalkan.',
        function(done) {
            apiFetch(ROUTES.delete + '/' + id, 'DELETE').then(d => {
                done();
                if (d.success) { showFlash(d.message); doSearch(); }
                else showFlash(d.message || 'Gagal menghapus.', 'error');
            }).catch(function() { done(); showFlash('Terjadi kesalahan, coba lagi.', 'error'); });
        }
    );
}

// ── Reset Password ────────────────────────────────────
function resetPasswordUser(id, name) {
    document.getElementById('reset-id').value = id;
    document.getElementById('reset-name').textContent = name;
    document.getElementById('reset-password').value = '';
    document.getElementById('reset-password-confirmation').value = '';
    const err = document.getElementById('err-reset-password');
    if (err) { err.style.display = 'none'; err.textContent = ''; }
    openModal('m-reset');
}
function submitReset(e) {
    e.preventDefault();
    const id  = document.getElementById('reset-id').value;
    const body = {
        password:              document.getElementById('reset-password').value,
        password_confirmation: document.getElementById('reset-password-confirmation').value,
    };
    apiFetch(ROUTES.reset + '/' + id + '/reset-password', 'POST', body).then(d => {
        if (d.success) { closeModal('m-reset'); showFlash(d.message); }
        else {
            const err = document.getElementById('err-reset-password');
            if (err && d.errors && d.errors.password) {
                err.textContent = d.errors.password[0];
                err.style.display = 'block';
            } else {
                showFlash(d.message || 'Gagal reset password.', 'error');
            }
        }
    });
}

// ── Show inline errors ────────────────────────────────
function showErrors(prefix, errors) {
    ['name','email','role','password'].forEach(f => {
        const el = document.getElementById('err-' + prefix + '-' + f);
        if (!el) return;
        if (errors[f]) {
            el.textContent = errors[f][0];
            el.style.display = 'block';
            const inp = document.getElementById(prefix + '-' + f);
            if (inp) inp.classList.add('is-invalid');
        } else {
            el.textContent = '';
            el.style.display = 'none';
            const inp = document.getElementById(prefix + '-' + f);
            if (inp) inp.classList.remove('is-invalid');
        }
    });
}
</script>
@endpush
