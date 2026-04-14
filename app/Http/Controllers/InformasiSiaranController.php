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

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $list = $query->orderBy('tanggal', 'desc')->orderBy('waktu', 'desc')->get();

        return response()->json([
            'success' => true,
            'total'   => $list->count(),
            'data'    => $list,
        ]);
    }
}