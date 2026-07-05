<?php

namespace App\Http\Controllers;

use App\Models\narasumberbyphone;
use App\Models\narasumbertalkshow;
use App\Models\informasisiaran;
use App\Models\BroadcastSession;
use App\Services\ExportService;
use App\Services\JadwalValidationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NarasumberByphoneController extends Controller
{
    /**
     * Dashboard untuk GATEKEEPER.
     * Statistik: informasi siaran + narasumber byphone.
     * Fitur: Informasi Siaran + Narasumber Byphone.
     *
     * Route: GET /gatekeeper/dashboard
     * Middleware: isGatekeeper
     */
    public function dashboardGatekeeper(): View
    {
        $narasumberList   = narasumberbyphone::orderBy('waktu_wawancara', 'desc')->paginate(15);
        $narasumberRecent = narasumberbyphone::orderBy('waktu_wawancara', 'desc')->take(5)->get();

        $siaranList   = informasisiaran::orderBy('tanggal', 'desc')->orderBy('waktu', 'desc')->paginate(15);
        $siaranRecent = informasisiaran::orderBy('tanggal', 'desc')->orderBy('waktu', 'desc')->take(5)->get();

        // Siaran yang sedang aktif (untuk kartu status broadcast)
        $activeSiaran = informasisiaran::where('status', 'active')->latest('started_at')->first();

        // Daftar siaran pending untuk dropdown modal mulai siaran
        $pendingSiaranList = informasisiaran::where('status', 'pending')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->get();

        // Ambil semua stats siaran dalam 1 query (bukan 3 query terpisah)
        $siaranStats = informasisiaran::selectRaw("
            COUNT(*) as total_siaran,
            SUM(CASE WHEN DATE(tanggal) = CURDATE() THEN 1 ELSE 0 END) as siaran_hari_ini,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as siaran_selesai
        ")->first();

        // Ambil semua stats narasumber dalam 1 query (bukan 3 query terpisah)
        $narasumberStats = narasumberbyphone::selectRaw("
            COUNT(*) as total_narasumber,
            SUM(CASE WHEN DATE(waktu_wawancara) = CURDATE() THEN 1 ELSE 0 END) as wawancara_hari_ini,
            SUM(CASE WHEN DATE(waktu_wawancara) < CURDATE() THEN 1 ELSE 0 END) as wawancara_selesai
        ")->first();

        $stats = [
            'total_siaran'       => (int) $siaranStats->total_siaran,
            'total_narasumber'   => (int) $narasumberStats->total_narasumber,
            'siaran_hari_ini'    => (int) $siaranStats->siaran_hari_ini,
            'wawancara_hari_ini' => (int) $narasumberStats->wawancara_hari_ini,
            'siaran_selesai'     => (int) $siaranStats->siaran_selesai,
            'wawancara_selesai'  => (int) $narasumberStats->wawancara_selesai,
        ];

        return view('gatekeeper.dashboard', compact(
            'narasumberList',
            'narasumberRecent',
            'siaranList',
            'siaranRecent',
            'stats',
            'activeSiaran',
            'pendingSiaranList'
        ));
    }

    /**
     * Dashboard untuk PENYIAR.
     * Statistik: narasumber byphone + narasumber talkshow.
     * Fitur: Narasumber Byphone + Narasumber Talkshow.
     *
     * Route: GET /penyiar/dashboard
     * Middleware: isPenyiar
     */
    public function dashboardPenyiar(): View
    {
        // ── Narasumber Byphone ────────────────────────────────────────
        $byphoneList   = narasumberbyphone::orderBy('waktu_wawancara', 'desc')->paginate(15);
        $byphoneRecent = narasumberbyphone::orderBy('waktu_wawancara', 'desc')->take(5)->get();

        // ── Narasumber Talkshow ───────────────────────────────────────
        $talkshowList   = narasumbertalkshow::orderBy('waktu_wawancara', 'desc')->paginate(15);
        $talkshowRecent = narasumbertalkshow::orderBy('waktu_wawancara', 'desc')->take(5)->get();

        // Ambil semua stats dalam 2 query (bukan 4 query terpisah)
        $byphoneStats = narasumberbyphone::selectRaw("
            COUNT(*) as total_byphone,
            SUM(CASE WHEN DATE(waktu_wawancara) = CURDATE() THEN 1 ELSE 0 END) as byphone_hari_ini
        ")->first();

        $talkshowStats = narasumbertalkshow::selectRaw("
            COUNT(*) as total_talkshow,
            SUM(CASE WHEN DATE(waktu_wawancara) = CURDATE() THEN 1 ELSE 0 END) as talkshow_hari_ini
        ")->first();

        $stats = [
            'total_byphone'     => (int) $byphoneStats->total_byphone,
            'total_talkshow'    => (int) $talkshowStats->total_talkshow,
            'byphone_hari_ini'  => (int) $byphoneStats->byphone_hari_ini,
            'talkshow_hari_ini' => (int) $talkshowStats->talkshow_hari_ini,
        ];

        // ── Siaran aktif ──────────────────────────────────────────────
        $activeBroadcast = BroadcastSession::where('penyiar_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if ($activeBroadcast) {
            $activeBroadcast->narasumber_detail = $activeBroadcast->narasumber;
        }

        // ── Attach broadcast_status ke byphoneList ────────────────────
        $bpIds = $byphoneList->pluck('byphoneID')->toArray();
        $bpSessions = BroadcastSession::where('narasumber_type', 'byphone')
            ->whereIn('narasumber_id', $bpIds)
            ->whereDate('started_at', today())
            ->get()
            ->groupBy('narasumber_id');

        $byphoneList->getCollection()->transform(function ($n) use ($bpSessions) {
            $sessions = $bpSessions->get($n->byphoneID, collect());
            if ($sessions->contains('status', 'active'))         $n->broadcast_status = 'sedang_siaran';
            elseif ($sessions->contains('status', 'completed'))  $n->broadcast_status = 'sudah_siaran';
            else                                                  $n->broadcast_status = 'belum_siaran';
            return $n;
        });

        // ── Attach broadcast_status ke talkshowList ───────────────────
        $tsIds = $talkshowList->pluck('talkshowID')->toArray();
        $tsSessions = BroadcastSession::where('narasumber_type', 'talkshow')
            ->whereIn('narasumber_id', $tsIds)
            ->whereDate('started_at', today())
            ->get()
            ->groupBy('narasumber_id');

        $talkshowList->getCollection()->transform(function ($n) use ($tsSessions) {
            $sessions = $tsSessions->get($n->talkshowID, collect());
            if ($sessions->contains('status', 'active'))         $n->broadcast_status = 'sedang_siaran';
            elseif ($sessions->contains('status', 'completed'))  $n->broadcast_status = 'sudah_siaran';
            else                                                  $n->broadcast_status = 'belum_siaran';
            return $n;
        });

        return view('penyiar.dashboard', compact(
            'byphoneList',
            'byphoneRecent',
            'talkshowList',
            'talkshowRecent',
            'stats',
            'activeBroadcast'
        ));
    }

    /**
     * Simpan data narasumber byphone baru.
     * Digunakan oleh gatekeeper dan penyiar.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:150',
            'instansi'         => 'required|string|max:150',
            'nomor_telp'       => 'required|string|max:20',
            'tema_wawancara'   => 'required|string|max:255',
            'waktu_wawancara'  => 'required|date_format:Y-m-d H:i',
            'draft_pertanyaan' => 'nullable|string|max:3000',
        ], [
            'nama.required'                   => 'Nama narasumber wajib diisi.',
            'instansi.required'               => 'Instansi wajib diisi.',
            'nomor_telp.required'             => 'Nomor telepon wajib diisi.',
            'tema_wawancara.required'         => 'Tema wawancara wajib diisi.',
            'waktu_wawancara.required'        => 'Waktu wawancara wajib diisi.',
            'waktu_wawancara.date_format'     => 'Format waktu wawancara tidak valid (gunakan format YYYY-MM-DD HH:MM).',
        ]);

        $narasumber = narasumberbyphone::create($validated + ['user_id' => auth()->id()]);

        return response()->json([
            'success' => true,
            'message' => 'Data narasumber byphone berhasil ditambahkan.',
            'data'    => $narasumber,
        ], 201);
    }

    /**
     * Tampilkan detail satu narasumber byphone.
     */
    public function show(string $id): JsonResponse
    {
        $narasumber = narasumberbyphone::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $narasumber,
        ]);
    }

    /**
     * Perbarui data narasumber byphone.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $narasumber = narasumberbyphone::findOrFail($id);

        $validSchedules = array_keys(\App\Services\JadwalValidationService::SCHEDULES);

        $validated = $request->validate([
            'nama'             => 'required|string|max:150',
            'instansi'         => 'required|string|max:150',
            'nomor_telp'       => 'required|string|max:20',
            'tema_wawancara'   => 'required|string|max:255',
            'waktu_wawancara'  => 'required|date_format:Y-m-d H:i',
            'draft_pertanyaan' => 'nullable|string|max:3000',
            'byphone_schedule' => ['nullable', 'string', \Illuminate\Validation\Rule::in($validSchedules)],
        ], [
            'nama.required'                   => 'Nama narasumber wajib diisi.',
            'instansi.required'               => 'Instansi wajib diisi.',
            'nomor_telp.required'             => 'Nomor telepon wajib diisi.',
            'tema_wawancara.required'         => 'Tema wawancara wajib diisi.',
            'waktu_wawancara.required'        => 'Waktu wawancara wajib diisi.',
            'waktu_wawancara.date_format'     => 'Format waktu wawancara tidak valid (gunakan format YYYY-MM-DD HH:MM).',
        ]);

        $newSchedule = $validated['byphone_schedule'] ?? null;
        if ($newSchedule !== null) {
            $svc = new \App\Services\JadwalValidationService();
            $jam     = $svc->extractJam($validated['waktu_wawancara']);
            $tanggal = $svc->extractTanggal($validated['waktu_wawancara']);
            $result  = $svc->isJamByphoneAvailable($jam, $tanggal, (int) $narasumber->byphoneID);

            if ($result !== true) {
                return response()->json([
                    'success' => false,
                    'message' => $result,
                ], 422);
            }
        }

        $narasumber->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data narasumber byphone berhasil diperbarui.',
            'data'    => $narasumber->fresh(),
        ]);
    }

    /**
     * Hapus data narasumber byphone.
     */
    public function destroy(string $id): JsonResponse
    {
        $narasumber = narasumberbyphone::findOrFail($id);

        // BUG-04 FIX: Cek apakah narasumber sedang dalam sesi siaran aktif
        $activeBroadcast = BroadcastSession::where('narasumber_type', 'byphone')
            ->where('narasumber_id', $id)
            ->where('status', 'active')
            ->exists();

        if ($activeBroadcast) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus narasumber yang sedang dalam sesi siaran aktif.',
            ], 422);
        }

        $narasumber->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data narasumber byphone berhasil dihapus.',
        ]);
    }

    /**
     * Cari & filter narasumber byphone (AJAX).
     *
     * Query param `today_only=true` membatasi hasil hanya untuk hari ini
     * dan menambahkan field `broadcast_status` pada setiap item.
     */
    public function search(Request $request): JsonResponse
    {
        $query = narasumberbyphone::query();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                  ->orWhere('instansi', 'like', "%{$keyword}%")
                  ->orWhere('tema_wawancara', 'like', "%{$keyword}%")
                  ->orWhere('byphone_schedule', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('instansi')) {
            $query->where('instansi', $request->instansi);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('waktu_wawancara', $request->tanggal);
        }

        if ($request->boolean('today_only')) {
            $query->whereDate('waktu_wawancara', today());
        }

        $perPage = in_array((int) $request->input('limit', 15), [10, 15, 25, 50, 100]) ? (int) $request->input('limit', 15) : 15;
        $list = $query->orderBy('waktu_wawancara', 'desc')->paginate($perPage);

        // Tambahkan broadcast_status untuk setiap narasumber (efisien: 1 query batch)
        if ($request->boolean('today_only') || $request->filled('tanggal')) {
            $narasumberIds = $list->pluck('byphoneID')->toArray();

            $todaySessions = BroadcastSession::where('narasumber_type', 'byphone')
                ->whereIn('narasumber_id', $narasumberIds)
                ->whereDate('started_at', today())
                ->get()
                ->groupBy('narasumber_id');

            $list->getCollection()->transform(function ($n) use ($todaySessions) {
                $sessions = $todaySessions->get($n->byphoneID, collect());

                if ($sessions->contains('status', 'active')) {
                    $n->broadcast_status = 'sedang_siaran';
                } elseif ($sessions->contains('status', 'completed')) {
                    $n->broadcast_status = 'sudah_siaran';
                } else {
                    $n->broadcast_status = 'belum_siaran';
                }

                return $n;
            });
        }

        return response()->json([
            'success'      => true,
            'total'        => $list->total(),
            'from'         => $list->firstItem(),
            'to'           => $list->lastItem(),
            'current_page' => $list->currentPage(),
            'last_page'    => $list->lastPage(),
            'data'         => $list->items(),
        ]);
    }

    /**
     * Statistik terkini untuk dashboard gatekeeper (AJAX).
     *
     * Route: GET /gatekeeper/stats
     * Middleware: isGatekeeper
     */
    public function getStats(): JsonResponse
    {
        // Ambil semua stats dalam 2 query (bukan 6 query terpisah)
        $siaranStats = informasisiaran::selectRaw("
            COUNT(*) as total_siaran,
            SUM(CASE WHEN DATE(tanggal) = CURDATE() THEN 1 ELSE 0 END) as siaran_hari_ini,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as siaran_selesai
        ")->first();

        $narasumberStats = narasumberbyphone::selectRaw("
            COUNT(*) as total_narasumber,
            SUM(CASE WHEN DATE(waktu_wawancara) = CURDATE() THEN 1 ELSE 0 END) as wawancara_hari_ini,
            SUM(CASE WHEN DATE(waktu_wawancara) < CURDATE() THEN 1 ELSE 0 END) as wawancara_selesai
        ")->first();

        return response()->json([
            'success' => true,
            'data'    => [
                'total_siaran'       => (int) $siaranStats->total_siaran,
                'total_narasumber'   => (int) $narasumberStats->total_narasumber,
                'siaran_hari_ini'    => (int) $siaranStats->siaran_hari_ini,
                'wawancara_hari_ini' => (int) $narasumberStats->wawancara_hari_ini,
                'siaran_selesai'     => (int) $siaranStats->siaran_selesai,
                'wawancara_selesai'  => (int) $narasumberStats->wawancara_selesai,
            ],
        ]);
    }

    /**
     * Export data Gatekeeper ke Excel.
     *
     * Route: GET /gatekeeper/export?period=week|month|year
     * Middleware: isGatekeeper
     */
    public function exportGatekeeper(Request $request, ExportService $exportService): StreamedResponse
    {
        $period = in_array($request->query('period'), ['week', 'month', 'year'])
            ? $request->query('period')
            : 'month';

        return $exportService->exportGatekeeper($period);
    }

    /**
     * Export Narasumber Byphone saja ke Excel.
     *
     * Route: GET /gatekeeper/export-byphone?period=week|month|year
     * Middleware: isGatekeeper
     */
    public function exportByphone(Request $request, ExportService $exportService): StreamedResponse
    {
        $period = in_array($request->query('period'), ['week', 'month', 'year'])
            ? $request->query('period')
            : 'month';

        return $exportService->exportNarasumberByphone($period);
    }

    /**
     * Export Informasi Siaran saja ke Excel.
     *
     * Route: GET /gatekeeper/export-siaran?period=week|month|year
     * Middleware: isGatekeeper
     */
    public function exportSiaran(Request $request, ExportService $exportService): StreamedResponse
    {
        $period = in_array($request->query('period'), ['week', 'month', 'year'])
            ? $request->query('period')
            : 'month';

        return $exportService->exportInformasiSiaran($period);
    }

    /**
     * Daftar instansi unik untuk dropdown filter (AJAX).
     */
    public function instansiList(): JsonResponse
    {
        $instansi = narasumberbyphone::select('instansi')
            ->distinct()
            ->orderBy('instansi')
            ->pluck('instansi');

        return response()->json([
            'success' => true,
            'data'    => $instansi,
        ]);
    }

    /**
     * Update jadwal byphone untuk satu narasumber (AJAX).
     *
     * Route: PATCH /gatekeeper/narasumber-byphone/{id}/schedule
     * Middleware: isGatekeeper
     */
    public function updateSchedule(Request $request, string $id): JsonResponse
    {
        $validSchedules = array_keys(JadwalValidationService::SCHEDULES);

        $validated = $request->validate([
            'byphone_schedule' => ['nullable', 'string', \Illuminate\Validation\Rule::in($validSchedules)],
        ]);

        $narasumber = narasumberbyphone::findOrFail($id);

        // Anti-collision: only validate when setting a schedule (not when clearing)
        $newSchedule = $validated['byphone_schedule'] ?? null;
        if ($newSchedule !== null) {
            $svc = new JadwalValidationService();
            $jam     = $svc->extractJam($narasumber->waktu_wawancara);
            $tanggal = $svc->extractTanggal($narasumber->waktu_wawancara);
            $result  = $svc->isJamByphoneAvailable($jam, $tanggal, (int) $narasumber->byphoneID);

            if ($result !== true) {
                return response()->json([
                    'success' => false,
                    'message' => $result,
                ], 422);
            }
        }

        $narasumber->update(['byphone_schedule' => $newSchedule]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal byphone berhasil disimpan.',
            'data'    => $narasumber->fresh(),
        ]);
    }
}