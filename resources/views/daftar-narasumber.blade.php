<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Sebagai Narasumber — {{ config('app.name', 'Radio RKB') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('/image/LOGO.png') }}">
    <link rel="shortcut icon" href="{{ asset('/image/LOGO.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent:      #7c6ce0;
            --accent-soft: #a99df5;
            --card-bg:     rgba(255,255,255,0.52);
            --card-border: rgba(255,255,255,0.78);
            --text-dark:   #2d2a45;
            --text-mid:    #5c5880;
            --text-soft:   #8e8ab5;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Aurora Background ── */
        .aurora-bg {
            position: fixed; inset: 0;
            background: linear-gradient(145deg, #ddeeff 0%, #e8e2fa 30%, #f0e8ff 55%, #fce8d8 75%, #d8f0ec 100%);
            z-index: 0;
        }
        .aurora-bg::before, .aurora-bg::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            mix-blend-mode: multiply;
            opacity: 0.7;
            animation: drift 14s ease-in-out infinite alternate;
        }
        .aurora-bg::before {
            width: 65vw; height: 65vw;
            background: radial-gradient(circle, rgba(160,140,255,0.55) 0%, rgba(100,180,255,0.3) 50%, transparent 75%);
            top: -15%; right: -10%;
            animation-duration: 12s;
        }
        .aurora-bg::after {
            width: 55vw; height: 55vw;
            background: radial-gradient(circle, rgba(255,210,160,0.45) 0%, rgba(180,230,220,0.35) 50%, transparent 72%);
            bottom: -15%; left: -10%;
            animation-duration: 16s;
            animation-delay: -6s;
        }
        @keyframes drift {
            0%   { transform: translate(0, 0)   scale(1); }
            33%  { transform: translate(3%, -4%) scale(1.04); }
            66%  { transform: translate(-2%, 3%) scale(0.97); }
            100% { transform: translate(4%, 2%)  scale(1.06); }
        }

        /* ── Page Wrapper ── */
        .page-wrap {
            position: relative; z-index: 2;
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* ── Navbar ── */
        .navbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.1rem 2.5rem;
            background: rgba(255,255,255,0.55);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.7);
            position: sticky; top: 0; z-index: 10;
        }
        .nav-brand {
            display: flex; align-items: center; gap: 0.65rem;
            text-decoration: none;
        }
        .nav-brand img { height: 38px; }
        .nav-brand-text {
            font-size: 0.95rem; font-weight: 700;
            color: var(--text-dark);
        }
        .btn-nav {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.5rem 1.3rem; border-radius: 10px; border: none;
            font-family: inherit; font-size: 0.88rem; font-weight: 600;
            cursor: pointer; text-decoration: none;
            background: linear-gradient(135deg, #9b8af4 0%, #7ab3f5 100%);
            color: #fff;
            box-shadow: 0 3px 12px rgba(140,110,240,0.28);
            transition: transform 0.18s, box-shadow 0.2s;
        }
        .btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(140,110,240,0.36);
        }
        .btn-nav svg { width: 15px; height: 15px; }

        /* ── Main Content ── */
        .main-content {
            flex: 1;
            display: flex; align-items: flex-start; justify-content: center;
            padding: 3rem 1.5rem 4rem;
        }

        /* ── Form Card ── */
        .form-card {
            width: 100%;
            max-width: 680px;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(28px) saturate(1.8);
            border: 1.5px solid rgba(255,255,255,0.85);
            border-radius: 24px;
            padding: 2.5rem 2.8rem;
            box-shadow: 0 16px 60px rgba(120,100,200,0.18), 0 2px 0 rgba(255,255,255,0.9) inset;
        }

        .form-card-header {
            margin-bottom: 1.8rem;
        }
        .form-card-title {
            font-size: 1.4rem; font-weight: 800; color: var(--text-dark);
            margin-bottom: 0.3rem;
        }
        .form-card-subtitle {
            font-size: 0.88rem; color: var(--text-soft);
        }

        /* Tabs */
        .form-tabs {
            display: flex; gap: 0.4rem;
            background: rgba(124,108,224,0.07);
            border-radius: 12px; padding: 0.3rem;
            margin-bottom: 1.6rem;
        }
        .form-tab {
            flex: 1; padding: 0.65rem 0.5rem;
            border: none; border-radius: 9px;
            font-family: inherit; font-size: 0.88rem; font-weight: 600;
            cursor: pointer; background: transparent;
            color: var(--text-soft);
            transition: background 0.18s, color 0.18s;
        }
        .form-tab.active {
            background: #fff;
            color: var(--accent);
            box-shadow: 0 2px 8px rgba(124,108,224,0.15);
        }

        /* Form fields */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        .form-field { display: flex; flex-direction: column; gap: 0.35rem; }
        .form-label {
            font-size: 0.82rem; font-weight: 600; color: var(--text-mid);
        }
        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1.5px solid rgba(124,108,224,0.2);
            border-radius: 10px;
            font-family: inherit; font-size: 0.92rem; color: var(--text-dark);
            background: rgba(255,255,255,0.7);
            transition: border-color 0.18s, box-shadow 0.18s;
            outline: none;
        }
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            border-color: rgba(124,108,224,0.55);
            box-shadow: 0 0 0 3px rgba(124,108,224,0.1);
        }
        .form-textarea { resize: vertical; min-height: 90px; }
        .form-error { font-size: 0.77rem; color: #e05252; }

        .btn-submit {
            width: 100%; margin-top: 1.4rem;
            padding: 0.9rem; border-radius: 12px; border: none;
            font-family: inherit; font-size: 1rem; font-weight: 700;
            cursor: pointer;
            background: linear-gradient(135deg, #9b8af4 0%, #7ab3f5 100%);
            color: #fff;
            box-shadow: 0 4px 18px rgba(140,110,240,0.3);
            transition: transform 0.18s, box-shadow 0.2s;
        }
        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(140,110,240,0.38);
        }
        .btn-submit:disabled { opacity: 0.65; cursor: not-allowed; }

        /* Success state */
        .form-success {
            display: none; flex-direction: column; align-items: center;
            text-align: center; padding: 2rem 1rem;
        }
        .form-success.show { display: flex; }
        .success-icon {
            width: 64px; height: 64px; border-radius: 50%;
            background: linear-gradient(135deg, #a8f0c6 0%, #6ee0b5 100%);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.2rem;
            box-shadow: 0 4px 20px rgba(100,210,170,0.35);
        }
        .success-icon svg { width: 32px; height: 32px; color: #1a7a56; }
        .success-title { font-size: 1.15rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.5rem; }
        .success-msg { font-size: 0.9rem; color: var(--text-mid); line-height: 1.6; max-width: 380px; }
        .btn-back {
            margin-top: 1.5rem;
            display: inline-flex; align-items: center; gap: 0.45rem;
            padding: 0.7rem 2rem; border-radius: 10px; border: none;
            font-family: inherit; font-size: 0.95rem; font-weight: 600;
            cursor: pointer; text-decoration: none;
            background: linear-gradient(135deg, #9b8af4 0%, #7ab3f5 100%);
            color: #fff;
            box-shadow: 0 4px 14px rgba(140,110,240,0.28);
            transition: transform 0.18s, box-shadow 0.2s;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(140,110,240,0.35);
        }

        /* ── Footer ── */
        footer {
            text-align: center;
            padding: 1.2rem;
            font-size: 0.78rem; color: var(--text-soft);
            background: rgba(255,255,255,0.3);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255,255,255,0.5);
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .navbar { padding: 0.9rem 1.2rem; }
            .main-content { padding: 2rem 1rem 3rem; }
            .form-card { padding: 1.8rem 1.4rem; }
        }
    </style>
</head>
<body>

<div class="aurora-bg"></div>

<div class="page-wrap">

    {{-- ── Navbar ── --}}
    <nav class="navbar">
        <a href="/" class="nav-brand">
            <img src="{{ asset('/image/LOGO RKB.png') }}" alt="Logo RKB">
            <span class="nav-brand-text">{{ config('app.name', 'Radio RKB') }}</span>
        </a>
        @auth
            <a href="{{ route('dashboard') }}" class="btn-nav">
                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="1" y="1" width="6" height="6" rx="1" stroke="white" stroke-width="1.4"/>
                    <rect x="9" y="1" width="6" height="6" rx="1" stroke="white" stroke-width="1.4"/>
                    <rect x="1" y="9" width="6" height="6" rx="1" stroke="white" stroke-width="1.4"/>
                    <rect x="9" y="9" width="6" height="6" rx="1" stroke="white" stroke-width="1.4"/>
                </svg>
                Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="btn-nav">
                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 2H12.5C13.3 2 14 2.7 14 3.5v9c0 .8-.7 1.5-1.5 1.5H10" stroke="white" stroke-width="1.4" stroke-linecap="round"/>
                    <path d="M6.5 11l3-3L6.5 5" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="9.5" y1="8" x2="2" y2="8" stroke="white" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
                Masuk
            </a>
        @endauth
    </nav>

    {{-- ── Main Content ── --}}
    <main class="main-content">
        <div class="form-card">

            {{-- Success State --}}
            <div class="form-success" id="formSuccess">
                <div class="success-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <div class="success-title">Pendaftaran Berhasil!</div>
                <div class="success-msg" id="successMsg">Data Anda telah diterima dan akan segera diproses oleh tim kami.</div>
                <a href="/" class="btn-back">
                    <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;">
                        <path d="M10 13L5 8l5-5" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Kembali ke Beranda
                </a>
            </div>

            {{-- Form Wrapper --}}
            <div id="formWrapper">
                <div class="form-card-header">
                    <div class="form-card-title">Daftar Sebagai Narasumber</div>
                    <div class="form-card-subtitle">Isi data di bawah untuk mendaftar sebagai narasumber siaran</div>
                </div>

                {{-- Tabs --}}
                <div class="form-tabs" role="tablist">
                    <button type="button" class="form-tab active" id="tabByphone" role="tab" aria-selected="true" data-tipe="byphone">
                        📞 Narasumber Byphone
                    </button>
                    <button type="button" class="form-tab" id="tabTalkshow" role="tab" aria-selected="false" data-tipe="talkshow">
                        🎙️ Narasumber Talkshow
                    </button>
                </div>

                {{-- Form Fields --}}
                <form id="formDaftarNarasumber" novalidate>
                    @csrf
                    <input type="hidden" name="tipe" id="fieldTipe" value="byphone">

                    <div class="form-grid">
                        <div class="form-field">
                            <label class="form-label" for="fieldNama">Nama Lengkap <span style="color:#e05252">*</span></label>
                            <input type="text" class="form-input" id="fieldNama" name="nama" placeholder="Nama lengkap Anda" maxlength="150">
                            <span class="form-error" id="errNama"></span>
                        </div>
                        <div class="form-field">
                            <label class="form-label" for="fieldInstansi">Instansi / Organisasi <span style="color:#e05252">*</span></label>
                            <input type="text" class="form-input" id="fieldInstansi" name="instansi" placeholder="Nama instansi Anda" maxlength="150">
                            <span class="form-error" id="errInstansi"></span>
                        </div>
                        <div class="form-field">
                            <label class="form-label" for="fieldTelp">Nomor Telepon <span style="color:#e05252">*</span></label>
                            <input type="tel" class="form-input" id="fieldTelp" name="nomor_telp" placeholder="08xxxxxxxxxx" maxlength="20">
                            <span class="form-error" id="errTelp"></span>
                        </div>
                        <div class="form-field">
                            <label class="form-label" for="fieldTema">Tema Wawancara <span style="color:#e05252">*</span></label>
                            <input type="text" class="form-input" id="fieldTema" name="tema_wawancara" placeholder="Topik yang akan dibahas" maxlength="255">
                            <span class="form-error" id="errTema"></span>
                        </div>
                        <div class="form-field">
                            <label class="form-label" for="fieldWaktu">Waktu Wawancara <span style="color:#e05252">*</span></label>
                            <input type="datetime-local" class="form-input" id="fieldWaktu" name="waktu_wawancara">
                            <span class="form-error" id="errWaktu"></span>
                        </div>
                        <div class="form-field">
                            <label class="form-label" for="fieldDraft">Draft Pertanyaan <span style="font-weight:400;color:var(--text-soft)">(opsional)</span></label>
                            <textarea class="form-textarea" id="fieldDraft" name="draft_pertanyaan" placeholder="Tuliskan poin-poin pertanyaan yang ingin dibahas..."></textarea>
                        </div>
                    </div>

                    <div class="form-error" id="errGeneral" style="margin-top:0.5rem;text-align:center;"></div>
                    <button type="submit" class="btn-submit" id="btnSubmit">
                        Kirim Pendaftaran
                    </button>
                </form>
            </div>

        </div>
    </main>

    {{-- ── Footer ── --}}
    <footer>
        &copy; {{ date('Y') }} {{ config('app.name', 'Radio RKB') }} — Aplikasi Manajemen Radio
    </footer>

</div>

<script>
(function () {
    var form         = document.getElementById('formDaftarNarasumber');
    var fieldTipe    = document.getElementById('fieldTipe');
    var formWrapper  = document.getElementById('formWrapper');
    var successPanel = document.getElementById('formSuccess');
    var successMsg   = document.getElementById('successMsg');
    var btnSubmit    = document.getElementById('btnSubmit');

    function clearErrors() {
        ['errNama','errInstansi','errTelp','errTema','errWaktu','errGeneral'].forEach(function(id){
            var el = document.getElementById(id);
            if (el) el.textContent = '';
        });
        form.querySelectorAll('.form-input,.form-textarea').forEach(function(el){
            el.style.borderColor = '';
        });
    }

    function setActiveTab(tipe) {
        document.querySelectorAll('.form-tab').forEach(function(tab){
            tab.classList.toggle('active', tab.dataset.tipe === tipe);
            tab.setAttribute('aria-selected', tab.dataset.tipe === tipe ? 'true' : 'false');
        });
        fieldTipe.value = tipe;
    }

    // Tab click
    document.querySelectorAll('.form-tab').forEach(function(tab){
        tab.addEventListener('click', function(){ setActiveTab(tab.dataset.tipe); });
    });

    // Form Submit
    form.addEventListener('submit', function(e){
        e.preventDefault();
        clearErrors();

        var data = new FormData(form);
        // datetime-local input sends "YYYY-MM-DDTHH:MM"; normalise to "YYYY-MM-DD HH:MM"
        var waktuField = document.getElementById('fieldWaktu');
        if (waktuField && waktuField.value) {
            data.set('waktu_wawancara', waktuField.value.replace('T', ' '));
        }
        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Mengirim…';

        fetch('{{ route('narasumber.self-register') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: data,
        })
        .then(function(res){ return res.json().then(function(json){ return { ok: res.ok, status: res.status, json: json }; }); })
        .then(function(result){
            if (result.ok && result.json.success) {
                successMsg.textContent = result.json.message;
                formWrapper.style.display = 'none';
                successPanel.classList.add('show');
            } else if (result.status === 422 && result.json.errors) {
                var map = {
                    'nama': 'errNama', 'instansi': 'errInstansi',
                    'nomor_telp': 'errTelp', 'tema_wawancara': 'errTema',
                    'waktu_wawancara': 'errWaktu',
                };
                Object.keys(result.json.errors).forEach(function(field){
                    var errId = map[field];
                    if (errId) {
                        var el = document.getElementById(errId);
                        if (el) el.textContent = result.json.errors[field][0];
                    } else {
                        var gen = document.getElementById('errGeneral');
                        if (gen) gen.textContent = result.json.errors[field][0];
                    }
                });
            } else {
                document.getElementById('errGeneral').textContent = 'Terjadi kesalahan. Silakan coba lagi.';
            }
        })
        .catch(function(){
            document.getElementById('errGeneral').textContent = 'Koneksi bermasalah. Silakan coba lagi.';
        })
        .finally(function(){
            btnSubmit.disabled = false;
            btnSubmit.textContent = 'Kirim Pendaftaran';
        });
    });
})();
</script>

</body>
</html>
