<?php

namespace App\Http\Controllers;

use App\Models\narasumberbyphone;
use App\Models\narasumbertalkshow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NarasumberSelfRegistrationController extends Controller
{
    /**
     * Simpan pendaftaran narasumber secara mandiri (tanpa login).
     * Menerima tipe 'byphone' atau 'talkshow'.
     *
     * Route: POST /daftar-narasumber
     * Middleware: none (public)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipe'             => 'required|in:byphone,talkshow',
            'nama'             => 'required|string|max:150',
            'instansi'         => 'required|string|max:150',
            'nomor_telp'       => 'required|string|max:20',
            'tema_wawancara'   => 'required|string|max:255',
            'waktu_wawancara'  => 'required|date_format:Y-m-d H:i',
            'draft_pertanyaan' => 'nullable|string|max:3000',
        ], [
            'tipe.required'                   => 'Tipe narasumber wajib dipilih.',
            'tipe.in'                         => 'Tipe narasumber tidak valid.',
            'nama.required'                   => 'Nama narasumber wajib diisi.',
            'instansi.required'               => 'Instansi wajib diisi.',
            'nomor_telp.required'             => 'Nomor telepon wajib diisi.',
            'tema_wawancara.required'         => 'Tema wawancara wajib diisi.',
            'waktu_wawancara.required'        => 'Waktu wawancara wajib diisi.',
            'waktu_wawancara.date_format'     => 'Format waktu wawancara tidak valid (gunakan format YYYY-MM-DD HH:MM).',
        ]);

        $data = [
            'nama'               => $validated['nama'],
            'instansi'           => $validated['instansi'],
            'nomor_telp'         => $validated['nomor_telp'],
            'tema_wawancara'     => $validated['tema_wawancara'],
            'waktu_wawancara'    => $validated['waktu_wawancara'],
            'draft_pertanyaan'   => $validated['draft_pertanyaan'] ?? null,
            'is_self_registered' => true,
        ];

        if ($validated['tipe'] === 'byphone') {
            $narasumber = narasumberbyphone::create($data);
            $tipe_label = 'Narasumber Byphone';
        } else {
            $narasumber = narasumbertalkshow::create($data);
            $tipe_label = 'Narasumber Talkshow';
        }

        Log::info('Narasumber self-registration', [
            'tipe'      => $validated['tipe'],
            'nama'      => $validated['nama'],
            'instansi'  => $validated['instansi'],
            'id'        => $narasumber->getKey(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pendaftaran sebagai {$tipe_label} berhasil! Data Anda telah diterima.",
            'data'    => $narasumber,
        ], 201);
    }
}
