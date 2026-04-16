@extends('layouts.pjprogram')

@section('title', 'Profile')
@section('page-title', 'Profile Saya')
@section('page-sub', 'Kelola informasi akun Anda')

@section('content')

<div style="display:grid;grid-template-columns:260px 1fr;gap:20px;align-items:start;">

    {{-- ── Info Card ── --}}
    <div style="background:#fff;border-radius:10px;border:1px solid #e2e8f0;padding:24px;text-align:center;">
        <div style="width:64px;height:64px;border-radius:50%;background:#fffbeb;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;color:#d97706;margin:0 auto 14px;">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:3px;">{{ $user->name }}</div>
        <div style="font-size:12px;color:#64748b;margin-bottom:12px;">{{ $user->email }}</div>
        <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#fef3c7;color:#92400e;">
            PJ Program
        </span>
        <div style="margin-top:16px;border-top:1px solid #f1f5f9;padding-top:14px;">
            <div style="display:flex;justify-content:space-between;margin-bottom:7px;">
                <span style="font-size:11px;color:#64748b;">Bergabung</span>
                <span style="font-size:11px;color:#0f172a;font-weight:600;">{{ $user->created_at->format('d M Y') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="font-size:11px;color:#64748b;">Status</span>
                <span style="font-size:11px;color:#059669;font-weight:600;">Aktif</span>
            </div>
        </div>
    </div>

    {{-- ── Forms ── --}}
    <div>
        {{-- Update Profile --}}
        <div style="background:#fff;border-radius:10px;border:1px solid #e2e8f0;padding:24px;margin-bottom:20px;">
            <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:4px;">Informasi Profil</div>
            <div style="font-size:12px;color:#64748b;margin-bottom:18px;">Perbarui nama dan alamat email akun Anda.</div>

            @if(session('status') === 'profile-updated')
                <div class="alert alert-success" style="margin-bottom:14px;">
                    <svg width="14" height="14" viewBox="0 0 15 15" fill="none"><path d="M12.5 4.5l-7 7-3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Profil berhasil diperbarui.
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')
                <div class="fg">
                    <label class="fl">Nama Lengkap</label>
                    <input class="fi {{ $errors->get('name') ? 'is-invalid' : '' }}" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @if($errors->get('name'))
                        <div style="font-size:11px;color:#dc2626;margin-top:4px;">{{ $errors->first('name') }}</div>
                    @endif
                </div>
                <div class="fg">
                    <label class="fl">Alamat Email</label>
                    <input class="fi {{ $errors->get('email') ? 'is-invalid' : '' }}" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @if($errors->get('email'))
                        <div style="font-size:11px;color:#dc2626;margin-top:4px;">{{ $errors->first('email') }}</div>
                    @endif
                </div>
                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit" class="bsv">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        {{-- Update Password --}}
        <div style="background:#fff;border-radius:10px;border:1px solid #e2e8f0;padding:24px;">
            <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:4px;">Ubah Password</div>
            <div style="font-size:12px;color:#64748b;margin-bottom:18px;">Pastikan akun Anda menggunakan password yang kuat.</div>

            @if(session('status') === 'password-updated')
                <div class="alert alert-success" style="margin-bottom:14px;">
                    <svg width="14" height="14" viewBox="0 0 15 15" fill="none"><path d="M12.5 4.5l-7 7-3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Password berhasil diperbarui.
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')
                <div class="fg">
                    <label class="fl">Password Saat Ini</label>
                    <input class="fi {{ $errors->updatePassword->get('current_password') ? 'is-invalid' : '' }}" type="password" name="current_password" autocomplete="current-password">
                    @if($errors->updatePassword->get('current_password'))
                        <div style="font-size:11px;color:#dc2626;margin-top:4px;">{{ $errors->updatePassword->first('current_password') }}</div>
                    @endif
                </div>
                <div class="frow">
                    <div class="fg">
                        <label class="fl">Password Baru</label>
                        <input class="fi {{ $errors->updatePassword->get('password') ? 'is-invalid' : '' }}" type="password" name="password" autocomplete="new-password">
                        @if($errors->updatePassword->get('password'))
                            <div style="font-size:11px;color:#dc2626;margin-top:4px;">{{ $errors->updatePassword->first('password') }}</div>
                        @endif
                    </div>
                    <div class="fg">
                        <label class="fl">Konfirmasi Password</label>
                        <input class="fi" type="password" name="password_confirmation" autocomplete="new-password">
                    </div>
                </div>
                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit" class="bsv">Perbarui Password</button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection
