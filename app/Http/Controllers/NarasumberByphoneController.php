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
        $narasumberList   = narasumberbyphone::orderBy('waktu_wawancara', 'desc')->get();
        $narasumberRecent = narasumberbyphone::orderBy('waktu_wawancara', 'desc')->take(5)->get();

        $siaranList   = informasisiaran::orderBy('tanggal', 'desc')->orderBy('waktu', 'desc')->get();
        $siaranRecent = informasisiaran::orderBy('tanggal', 'desc')->orderBy('waktu', 'desc')->take(5)->get();

        $stats = [
            'total_siaran'       => informasisiaran::count(),
            'total_narasumber'   => narasumberbyphone::count(),
            'siaran_hari_ini'    => informasisiaran::whereDate('tanggal', today())->count(),
            'wawancara_hari_ini' => narasumberbyphone::whereDate('waktu_wawancara', today())->count(),
        ];

        return view('gatekeeper.dashboard', compact(
            'narasumberList',
            'narasumberRecent',
            'siaranList',
            'siaranRecent',
            'stats'
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
        $byphoneList   = narasumberbyphone::orderBy('waktu_wawancara', 'desc')->get();
        $byphoneRecent = narasumberbyphone::orderBy('waktu_wawancara', 'desc')->take(5)->get();

        // ── Narasumber Talkshow ───────────────────────────────────────
        $talkshowList   = narasumbertalkshow::orderBy('waktu_wawancara', 'desc')->get();
        $talkshowRecent = narasumbertalkshow::orderBy('waktu_wawancara', 'desc')->take(5)->get();

        // ── Statistik gabungan ────────────────────────────────────────
        $stats = [
            'total_byphone'       => narasumberbyphone::count(),
            'total_talkshow'      => narasumbertalkshow::count(),
            'byphone_hari_ini'    => narasumberbyphone::whereDate('waktu_wawancara', today())->count(),
            'talkshow_hari_ini'   => narasumbertalkshow::whereDate('waktu_wawancara', today())->count(),
        ];

        // ── Siaran aktif ──────────────────────────────────────────────
        $activeBroadcast = BroadcastSession::where('penyiar_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if ($activeBroadcast) {
            $activeBroadcast->narasumber_detail = $activeBroadcast->narasumber;
        }

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
            'draft_pertanyaan' => 'nullable|string',
        ], [
            'nama.required'                   => 'Nama narasumber wajib diisi.',
            'instansi.required'               => 'Instansi wajib diisi.',
            'nomor_telp.required'             => 'Nomor telepon wajib diisi.',
            'tema_wawancara.required'         => 'Tema wawancara wajib diisi.',
            'waktu_wawancara.required'        => 'Waktu wawancara wajib diisi.',
            'waktu_wawancara.date_format'     => 'Format waktu wawancara tidak valid (gunakan format YYYY-MM-DD HH:MM).',
        ]);

        $narasumber = narasumberbyphone::create($validated);

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

        $validated = $request->validate([
            'nama'             => 'required|string|max:150',
            'instansi'         => 'required|string|max:150',
            'nomor_telp'       => 'required|string|max:20',
            'tema_wawancara'   => 'required|string|max:255',
            'waktu_wawancara'  => 'required|date_format:Y-m-d H:i',
            'draft_pertanyaan' => 'nullable|string',
        ], [
            'nama.required'                   => 'Nama narasumber wajib diisi.',
            'instansi.required'               => 'Instansi wajib diisi.',
            'nomor_telp.required'             => 'Nomor telepon wajib diisi.',
            'tema_wawancara.required'         => 'Tema wawancara wajib diisi.',
            'waktu_wawancara.required'        => 'Waktu wawancara wajib diisi.',
            'waktu_wawancara.date_format'     => 'Format waktu wawancara tidak valid (gunakan format YYYY-MM-DD HH:MM).',
        ]);

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
                  ->orWhere('tema_wawancara', 'like', "%{$keyword}%");
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

        $list = $query->orderBy('waktu_wawancara', 'asc')->get();

        // Tambahkan broadcast_status untuk setiap narasumber
        if ($request->boolean('today_only') || $request->filled('tanggal')) {
            $narasumberIds = $list->pluck('byphoneID')->toArray();

            $todaySessions = BroadcastSession::where('narasumber_type', 'byphone')
                ->whereIn('narasumber_id', $narasumberIds)
                ->whereDate('started_at', today())
                ->get()
                ->groupBy('narasumber_id');

            $list = $list->map(function ($n) use ($todaySessions) {
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
            'success' => true,
            'total'   => $list->count(),
            'data'    => $list,
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
        return response()->json([
            'success' => true,
            'data'    => [
                'total_siaran'       => informasisiaran::count(),
                'total_narasumber'   => narasumberbyphone::count(),
                'siaran_hari_ini'    => informasisiaran::whereDate('tanggal', today())->count(),
                'wawancara_hari_ini' => narasumberbyphone::whereDate('waktu_wawancara', today())->count(),
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
            $jam = (new JadwalValidationService())->extractJam($narasumber->waktu_wawancara);
            $result = (new JadwalValidationService())->isJamByphoneAvailable($jam, (int) $narasumber->byphoneID);

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