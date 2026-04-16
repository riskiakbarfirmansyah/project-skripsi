<?php

namespace App\Http\Controllers;

use App\Models\narasumbertalkshow;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

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
        $narasumberList   = narasumbertalkshow::orderBy('waktu_wawancara', 'desc')->get();
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
     */
    public function search(Request $request): JsonResponse
    {
        $query = narasumbertalkshow::query();

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

        $list = $query->orderBy('waktu_wawancara', 'desc')->get();

        return response()->json([
            'success' => true,
            'total'   => $list->count(),
            'data'    => $list,
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