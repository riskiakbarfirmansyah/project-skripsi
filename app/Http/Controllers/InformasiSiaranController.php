<?php

namespace App\Http\Controllers;

use App\Models\informasisiaran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InformasiSiaranController extends Controller
{
    /**
     * Simpan data siaran baru.
     * Hanya digunakan oleh gatekeeper.
     * index() tidak diperlukan karena ditangani oleh
     * NarasumberByphoneController@dashboardGatekeeper.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tanggal'    => 'required|date',
            'waktu'      => 'required|date_format:H:i',
            'keterangan' => 'required|string|max:500',
        ], [
            'tanggal.required'    => 'Tanggal wajib diisi.',
            'tanggal.date'        => 'Format tanggal tidak valid.',
            'waktu.required'      => 'Waktu wajib diisi.',
            'waktu.date_format'   => 'Format waktu tidak valid (HH:MM).',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'keterangan.max'      => 'Keterangan maksimal 500 karakter.',
        ]);

        $siaran = informasisiaran::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data siaran berhasil ditambahkan.',
            'data'    => $siaran,
        ], 201);
    }

    /**
     * Tampilkan detail satu siaran.
     */
    public function show(string $id): JsonResponse
    {
        $siaran = informasisiaran::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $siaran,
        ]);
    }

    /**
     * Perbarui data siaran.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $siaran = informasisiaran::findOrFail($id);

        $validated = $request->validate([
            'tanggal'    => 'required|date',
            'waktu'      => 'required|date_format:H:i',
            'keterangan' => 'required|string|max:500',
        ], [
            'tanggal.required'    => 'Tanggal wajib diisi.',
            'tanggal.date'        => 'Format tanggal tidak valid.',
            'waktu.required'      => 'Waktu wajib diisi.',
            'waktu.date_format'   => 'Format waktu tidak valid (HH:MM).',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'keterangan.max'      => 'Keterangan maksimal 500 karakter.',
        ]);

        $siaran->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data siaran berhasil diperbarui.',
            'data'    => $siaran->fresh(),
        ]);
    }

    /**
     * Hapus data siaran.
     */
    public function destroy(string $id): JsonResponse
    {
        $siaran = informasisiaran::findOrFail($id);
        $siaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data siaran berhasil dihapus.',
        ]);
    }

    /**
     * Mulai siaran – ubah status menjadi active.
     */
    public function startSiaran(string $id): JsonResponse
    {
        $siaran = informasisiaran::findOrFail($id);

        if ($siaran->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Siaran ini sudah selesai.',
            ], 422);
        }

        if ($siaran->status === 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Siaran sudah sedang berlangsung.',
            ], 422);
        }

        $siaran->update([
            'status'     => 'active',
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Siaran dimulai.',
            'data'    => $siaran->fresh(),
        ]);
    }

    /**
     * Akhiri siaran – ubah status menjadi completed.
     */
    public function endSiaran(string $id): JsonResponse
    {
        $siaran = informasisiaran::findOrFail($id);

        if ($siaran->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Siaran belum dimulai atau sudah selesai.',
            ], 422);
        }

        $siaran->update([
            'status'   => 'completed',
            'ended_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Siaran telah selesai.',
            'data'    => $siaran->fresh(),
        ]);
    }

    /**
     * Cari & filter siaran (AJAX).
     */
    public function search(Request $request): JsonResponse
    {
        $query = informasisiaran::query();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('keterangan', 'like', "%{$keyword}%")
                  ->orWhere('siaranID', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $perPage = in_array((int) $request->input('limit', 15), [10, 15, 25, 50, 100]) ? (int) $request->input('limit', 15) : 15;
        $list = $query->orderBy('tanggal', 'desc')->orderBy('waktu', 'desc')->paginate($perPage);

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
}