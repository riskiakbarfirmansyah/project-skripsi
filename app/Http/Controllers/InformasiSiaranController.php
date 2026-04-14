<?php

namespace App\Http\Controllers;

use App\Models\informasisiaran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class InformasiSiaranController extends Controller
{
    /**
     * Tampilkan halaman dashboard + daftar semua siaran.
     */
    public function index(): View
    {
        $siaranList = informasisiaran::orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->get();
 
        $stats = [
            'total'    => informasisiaran::count(),
            'hari_ini' => informasisiaran::whereDate('tanggal', today())->count(),
            'mendatang' => informasisiaran::whereDate('tanggal', '>', today())->count(),
            'selesai'  => informasisiaran::whereDate('tanggal', '<', today())->count(),
        ];
 
        $recent = informasisiaran::orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->take(5)
            ->get();
 
        return view('siaran.index', compact('siaranList', 'stats', 'recent'));
    }
 
    /**
     * Simpan data siaran baru ke database.
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
        ]);
 
        $siaran = informasisiaran::create($validated);
 
        return response()->json([
            'success' => true,
            'message' => 'Data siaran berhasil ditambahkan.',
            'data'    => $siaran,
        ], 201);
    }
 
    /**
     * Tampilkan detail satu siaran (untuk modal edit).
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
     * Perbarui data siaran yang sudah ada.
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
     * Hapus data siaran dari database.
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
     * Cari & filter siaran (untuk pencarian AJAX di frontend).
     */
    public function search(Request $request): JsonResponse
    {
        $query = informasisiaran::query();
 
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('keterangan', 'like', "%{$keyword}%")
                  ->orWhere('siaranID', 'like', "%{$keyword}%");
        }
 
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
 
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }
 
        $siaranList = $query->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->get();
 
        return response()->json([
            'success' => true,
            'total'   => $siaranList->count(),
            'data'    => $siaranList,
        ]);
    }
}
