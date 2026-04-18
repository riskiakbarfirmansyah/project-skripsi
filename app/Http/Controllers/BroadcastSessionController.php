<?php

namespace App\Http\Controllers;

use App\Models\BroadcastSession;
use App\Models\narasumberbyphone;
use App\Models\narasumbertalkshow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BroadcastSessionController extends Controller
{
    /**
     * Mulai siaran baru.
     *
     * Route: POST /penyiar/broadcast/start
     * Middleware: isPenyiar
     */
    public function startBroadcast(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'narasumber_type' => 'required|in:byphone,talkshow',
            'narasumber_id'   => 'required|integer',
        ]);

        // Pastikan tidak ada siaran aktif untuk penyiar ini
        $active = BroadcastSession::where('penyiar_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if ($active) {
            return response()->json([
                'success' => false,
                'message' => 'Anda masih memiliki siaran yang sedang aktif.',
            ], 422);
        }

        // Validasi narasumber ada
        if ($validated['narasumber_type'] === 'byphone') {
            $narasumber = narasumberbyphone::find($validated['narasumber_id']);
        } else {
            $narasumber = narasumbertalkshow::find($validated['narasumber_id']);
        }

        if (! $narasumber) {
            return response()->json([
                'success' => false,
                'message' => 'Narasumber tidak ditemukan.',
            ], 404);
        }

        $session = BroadcastSession::create([
            'penyiar_id'      => Auth::id(),
            'narasumber_type' => $validated['narasumber_type'],
            'narasumber_id'   => $validated['narasumber_id'],
            'started_at'      => now(),
            'status'          => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Siaran dimulai.',
            'data'    => $this->sessionData($session),
        ], 201);
    }

    /**
     * Selesaikan siaran aktif.
     *
     * Route: POST /penyiar/broadcast/end
     * Middleware: isPenyiar
     */
    public function endBroadcast(Request $request): JsonResponse
    {
        $session = BroadcastSession::where('penyiar_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada siaran yang sedang aktif.',
            ], 404);
        }

        $session->update([
            'ended_at' => now(),
            'status'   => 'completed',
            'notes'    => $request->input('notes'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Siaran selesai.',
            'data'    => $this->sessionData($session->fresh()),
        ]);
    }

    /**
     * Ambil siaran aktif milik penyiar saat ini.
     *
     * Route: GET /penyiar/broadcast/current
     * Middleware: isPenyiar
     */
    public function getCurrentBroadcast(): JsonResponse
    {
        $session = BroadcastSession::where('penyiar_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if (! $session) {
            return response()->json([
                'success' => true,
                'data'    => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->sessionData($session),
        ]);
    }

    /**
     * Riwayat siaran milik penyiar saat ini.
     *
     * Route: GET /penyiar/broadcast/history
     * Middleware: isPenyiar
     */
    public function getBroadcastHistory(): JsonResponse
    {
        $history = BroadcastSession::where('penyiar_id', Auth::id())
            ->where('status', 'completed')
            ->orderBy('started_at', 'desc')
            ->take(20)
            ->get()
            ->map(fn($s) => $this->sessionData($s));

        return response()->json([
            'success' => true,
            'data'    => $history,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────────────────────────

    private function sessionData(BroadcastSession $session): array
    {
        $narasumber = $session->narasumber;

        return [
            'id'               => $session->id,
            'narasumber_type'  => $session->narasumber_type,
            'narasumber_id'    => $session->narasumber_id,
            'started_at'       => $session->started_at?->toIso8601String(),
            'ended_at'         => $session->ended_at?->toIso8601String(),
            'status'           => $session->status,
            'notes'            => $session->notes,
            'duration_seconds' => $session->duration_in_seconds,
            'narasumber'       => $narasumber ? [
                'nama'            => $narasumber->nama,
                'instansi'        => $narasumber->instansi,
                'nomor_telp'      => $narasumber->nomor_telp,
                'tema_wawancara'  => $narasumber->tema_wawancara,
                'waktu_wawancara' => $narasumber->waktu_wawancara,
                'draft_pertanyaan' => $narasumber->draft_pertanyaan,
            ] : null,
        ];
    }
}
