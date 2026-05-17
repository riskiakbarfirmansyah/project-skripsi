<?php

namespace App\Http\Controllers;

use App\Models\BroadcastSession;
use App\Models\narasumbertalkshow;
use App\Services\ExportService;
use App\Services\JadwalValidationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NarasumberTalkshowController extends Controller
{
    /**
     * Dashboard untuk PJ_PROGRAM.
     * Hanya menampilkan data narasumber talkshow.
     *
     * Route: GET /pj-program/dashboard
     * Middleware: isPjProgram
     */
    public function dashboardPjProgram(): View
    {
        $narasumberList   = narasumbertalkshow::orderBy('waktu_wawancara', 'desc')->paginate(15);
        $narasumberRecent = narasumbertalkshow::orderBy('waktu_wawancara', 'desc')->take(5)->get();

        $stats = [
            'total_narasumber'    => narasumbertalkshow::count(),
            'wawancara_hari_ini'  => narasumbertalkshow::whereDate('waktu_wawancara', today())->count(),
            'wawancara_mendatang' => narasumbertalkshow::whereDate('waktu_wawancara', '>', today())
                                        ->whereDate('waktu_wawancara', '<=', today()->addDays(7))
                                        ->count(),
            'wawancara_selesai'   => narasumbertalkshow::whereDate('waktu_wawancara', '<', today())->count(),
        ];

        return view('pj_program.dashboard', compact(
            'narasumberList',
            'narasumberRecent',
            'stats'
        ));
    }

    /**
     * Simpan data narasumber talkshow baru.
     * Digunakan oleh pj_program dan penyiar.
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

        $narasumber = narasumbertalkshow::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data narasumber talkshow berhasil ditambahkan.',
            'data'    => $narasumber,
        ], 201);
    }

    /**
     * Tampilkan detail satu narasumber talkshow.
     */
    public function show(string $id): JsonResponse
    {
        $narasumber = narasumbertalkshow::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $narasumber,
        ]);
    }

    /**
     * Perbarui data narasumber talkshow.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $narasumber = narasumbertalkshow::findOrFail($id);

        $validSchedules = array_keys(\App\Services\JadwalValidationService::SCHEDULES);

        $validated = $request->validate([
            'nama'             => 'required|string|max:150',
            'instansi'         => 'required|string|max:150',
            'nomor_telp'       => 'required|string|max:20',
            'tema_wawancara'   => 'required|string|max:255',
            'waktu_wawancara'  => 'required|date_format:Y-m-d H:i',
            'draft_pertanyaan' => 'nullable|string',
            'talkshow_schedule'=> ['nullable', 'string', \Illuminate\Validation\Rule::in($validSchedules)],
        ], [
            'nama.required'                   => 'Nama narasumber wajib diisi.',
            'instansi.required'               => 'Instansi wajib diisi.',
            'nomor_telp.required'             => 'Nomor telepon wajib diisi.',
            'tema_wawancara.required'         => 'Tema wawancara wajib diisi.',
            'waktu_wawancara.required'        => 'Waktu wawancara wajib diisi.',
            'waktu_wawancara.date_format'     => 'Format waktu wawancara tidak valid (gunakan format YYYY-MM-DD HH:MM).',
        ]);

        $newSchedule = $validated['talkshow_schedule'] ?? null;
        if ($newSchedule !== null) {
            $svc = new \App\Services\JadwalValidationService();
            $jam     = $svc->extractJam($validated['waktu_wawancara']);
            $tanggal = $svc->extractTanggal($validated['waktu_wawancara']);
            $result  = $svc->isJamTalkshowAvailable($jam, $tanggal, (int) $narasumber->talkshowID);

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
            'message' => 'Data narasumber talkshow berhasil diperbarui.',
            'data'    => $narasumber->fresh(),
        ]);
    }

    /**
     * Hapus data narasumber talkshow.
     */
    public function destroy(string $id): JsonResponse
    {
        $narasumber = narasumbertalkshow::findOrFail($id);
        $narasumber->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data narasumber talkshow berhasil dihapus.',
        ]);
    }

    /**
     * Cari & filter narasumber talkshow (AJAX).
     *
     * Query param `today_only=true` membatasi hasil hanya untuk hari ini
     * dan menambahkan field `broadcast_status` pada setiap item.
     */
    public function search(Request $request): JsonResponse
    {
        $query = narasumbertalkshow::query();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                  ->orWhere('instansi', 'like', "%{$keyword}%")
                  ->orWhere('tema_wawancara', 'like', "%{$keyword}%")
                  ->orWhere('talkshow_schedule', 'like', "%{$keyword}%");
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
            $narasumberIds = $list->pluck('talkshowID')->toArray();

            $todaySessions = BroadcastSession::where('narasumber_type', 'talkshow')
                ->whereIn('narasumber_id', $narasumberIds)
                ->whereDate('started_at', today())
                ->get()
                ->groupBy('narasumber_id');

            $list = $list->map(function ($n) use ($todaySessions) {
                $sessions = $todaySessions->get($n->talkshowID, collect());

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

        $perPage = in_array((int) $request->input('limit', 15), [10, 15, 25, 50, 100]) ? (int) $request->input('limit', 15) : 15;
        $list = $query->orderBy('waktu_wawancara', 'desc')->paginate($perPage);

        return response()->json([
            'success'      => true,
            'total'        => $list->total(),
            'current_page' => $list->currentPage(),
            'last_page'    => $list->lastPage(),
            'data'         => $list->items(),
        ]);
    }

    /**
     * Export data PJ Program ke Excel.
     *
     * Route: GET /pj-program/export?period=week|month|year
     * Middleware: isPjProgram
     */
    public function exportPjProgram(Request $request, ExportService $exportService): StreamedResponse
    {
        $period = in_array($request->query('period'), ['week', 'month', 'year'])
            ? $request->query('period')
            : 'month';

        return $exportService->exportPjProgram($period);
    }

    /**
     * Export Narasumber Talkshow saja ke Excel.
     *
     * Route: GET /pj-program/export-talkshow?period=week|month|year
     * Middleware: isPjProgram
     */
    public function exportTalkshow(Request $request, ExportService $exportService): StreamedResponse
    {
        $period = in_array($request->query('period'), ['week', 'month', 'year'])
            ? $request->query('period')
            : 'month';

        return $exportService->exportNarasumberTalkshow($period);
    }

    /**
     * Update jadwal talkshow untuk satu narasumber (AJAX).
     *
     * Route: PATCH /pj-program/narasumber-talkshow/{id}/schedule
     * Middleware: isPjProgram
     */
    public function updateSchedule(Request $request, string $id): JsonResponse
    {
        $validSchedules = array_keys(JadwalValidationService::SCHEDULES);

        $validated = $request->validate([
            'talkshow_schedule' => ['nullable', 'string', \Illuminate\Validation\Rule::in($validSchedules)],
        ]);

        $narasumber = narasumbertalkshow::findOrFail($id);

        // Anti-collision: only validate when setting a schedule (not when clearing)
        $newSchedule = $validated['talkshow_schedule'] ?? null;
        if ($newSchedule !== null) {
            $svc = new JadwalValidationService();
            $jam     = $svc->extractJam($narasumber->waktu_wawancara);
            $tanggal = $svc->extractTanggal($narasumber->waktu_wawancara);
            $result  = $svc->isJamTalkshowAvailable($jam, $tanggal, (int) $narasumber->talkshowID);

            if ($result !== true) {
                return response()->json([
                    'success' => false,
                    'message' => $result,
                ], 422);
            }
        }

        $narasumber->update(['talkshow_schedule' => $newSchedule]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal talkshow berhasil disimpan.',
            'data'    => $narasumber->fresh(),
        ]);
    }

    /**
     * Daftar instansi unik untuk dropdown filter (AJAX).
     */
    public function instansiList(): JsonResponse
    {
        $instansi = narasumbertalkshow::select('instansi')
            ->distinct()
            ->orderBy('instansi')
            ->pluck('instansi');

        return response()->json([
            'success' => true,
            'data'    => $instansi,
        ]);
    }
}