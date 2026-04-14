<?php

namespace App\Http\Controllers;

use App\Models\narasumberbyphone;
use App\Models\informasisiaran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class NarasumberByphoneController extends Controller
{
     /**
     * Tampilkan halaman dashboard + daftar semua narasumber.
     */
    public function index(): View
    {
        $narasumberList = narasumberbyphone::orderBy('waktu_wawancara', 'desc')->get();
 
        $stats = [
            'total'     => narasumberbyphone::count(),
            'hari_ini'  => narasumberbyphone::whereDate('waktu_wawancara', today())->count(),
            'mendatang' => narasumberbyphone::whereDate('waktu_wawancara', '>', today())
                            ->whereDate('waktu_wawancara', '<=', today()->addDays(7))
                            ->count(),
            'selesai'   => narasumberbyphone::whereDate('waktu_wawancara', '<', today())->count(),
        ];
 
        $recent = narasumberbyphone::orderBy('waktu_wawancara', 'desc')->take(5)->get();
 
        return view('narasumber.index', compact('narasumberList', 'stats', 'recent'));
    }
 
    /**
     * Simpan data narasumber baru ke database.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:150',
            'instansi'         => 'required|string|max:150',
            'nomor_telp'       => 'required|string|max:20',
            'tema_wawancara'   => 'required|string|max:255',
            'waktu_wawancara'  => 'required|date',
            'draft_pertanyaan' => 'nullable|string',
        ], [
            'nama.required'            => 'Nama narasumber wajib diisi.',
            'instansi.required'        => 'Instansi wajib diisi.',
            'nomor_telp.required'      => 'Nomor telepon wajib diisi.',
            'tema_wawancara.required'  => 'Tema wawancara wajib diisi.',
            'waktu_wawancara.required' => 'Waktu wawancara wajib diisi.',
            'waktu_wawancara.date'     => 'Format waktu wawancara tidak valid.',
        ]);
 
        $narasumber = narasumberbyphone::create($validated);
 
        return response()->json([
            'success' => true,
            'message' => 'Data narasumber berhasil ditambahkan.',
            'data'    => $narasumber,
        ], 201);
    }
 
    /**
     * Tampilkan detail satu narasumber.
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
     * Perbarui data narasumber yang sudah ada.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $narasumber = narasumberbyphone::findOrFail($id);
 
        $validated = $request->validate([
            'nama'             => 'required|string|max:150',
            'instansi'         => 'required|string|max:150',
            'nomor_telp'       => 'required|string|max:20',
            'tema_wawancara'   => 'required|string|max:255',
            'waktu_wawancara'  => 'required|date',
            'draft_pertanyaan' => 'nullable|string',
        ], [
            'nama.required'            => 'Nama narasumber wajib diisi.',
            'instansi.required'        => 'Instansi wajib diisi.',
            'nomor_telp.required'      => 'Nomor telepon wajib diisi.',
            'tema_wawancara.required'  => 'Tema wawancara wajib diisi.',
            'waktu_wawancara.required' => 'Waktu wawancara wajib diisi.',
            'waktu_wawancara.date'     => 'Format waktu wawancara tidak valid.',
        ]);
 
        $narasumber->update($validated);
 
        return response()->json([
            'success' => true,
            'message' => 'Data narasumber berhasil diperbarui.',
            'data'    => $narasumber->fresh(),
        ]);
    }
 
    /**
     * Hapus data narasumber dari database.
     */
    public function destroy(string $id): JsonResponse
    {
        $narasumber = narasumberbyphone::findOrFail($id);
        $narasumber->delete();
 
        return response()->json([
            'success' => true,
            'message' => 'Data narasumber berhasil dihapus.',
        ]);
    }
 
    /**
     * Cari & filter narasumber (untuk pencarian AJAX di frontend).
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
 
        $list = $query->orderBy('waktu_wawancara', 'desc')->get();
 
        return response()->json([
            'success' => true,
            'total'   => $list->count(),
            'data'    => $list,
        ]);
    }
 
    /**
     * Ambil daftar instansi unik (untuk dropdown filter).
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
}
