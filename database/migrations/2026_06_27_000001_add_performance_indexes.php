<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan index pada kolom yang sering di-query untuk mempercepat performa.
     *
     * Kolom yang diindex:
     * - waktu_wawancara  → sering di whereDate(), orderBy()
     * - instansi         → sering di where() filter & distinct()
     * - nama             → sering di where LIKE search
     * - tanggal, status  → sering di whereDate() & where() pada informasi_siarans
     * - narasumber_type, narasumber_id, status → sering di BroadcastSessions
     */
    public function up(): void
    {
        // ── narasumber_byphones ──────────────────────────────────────────
        Schema::table('narasumber_byphones', function (Blueprint $table) {
            $table->index('waktu_wawancara', 'idx_byphone_waktu');
            $table->index('instansi',        'idx_byphone_instansi');
            $table->index('nama',            'idx_byphone_nama');
        });

        // ── narasumber_talkshows ─────────────────────────────────────────
        Schema::table('narasumber_talkshows', function (Blueprint $table) {
            $table->index('waktu_wawancara', 'idx_talkshow_waktu');
            $table->index('instansi',        'idx_talkshow_instansi');
            $table->index('nama',            'idx_talkshow_nama');
        });

        // ── informasi_siarans ────────────────────────────────────────────
        Schema::table('informasi_siarans', function (Blueprint $table) {
            $table->index('tanggal', 'idx_siaran_tanggal');
            $table->index('status',  'idx_siaran_status');
        });

        // ── broadcast_sessions ───────────────────────────────────────────
        Schema::table('broadcast_sessions', function (Blueprint $table) {
            $table->index(['narasumber_type', 'narasumber_id'], 'idx_broadcast_narasumber');
            $table->index('status',     'idx_broadcast_status');
            $table->index('started_at', 'idx_broadcast_started');
            $table->index('penyiar_id', 'idx_broadcast_penyiar');
        });
    }

    /**
     * Hapus index yang telah ditambahkan.
     */
    public function down(): void
    {
        Schema::table('narasumber_byphones', function (Blueprint $table) {
            $table->dropIndex('idx_byphone_waktu');
            $table->dropIndex('idx_byphone_instansi');
            $table->dropIndex('idx_byphone_nama');
        });

        Schema::table('narasumber_talkshows', function (Blueprint $table) {
            $table->dropIndex('idx_talkshow_waktu');
            $table->dropIndex('idx_talkshow_instansi');
            $table->dropIndex('idx_talkshow_nama');
        });

        Schema::table('informasi_siarans', function (Blueprint $table) {
            $table->dropIndex('idx_siaran_tanggal');
            $table->dropIndex('idx_siaran_status');
        });

        Schema::table('broadcast_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_broadcast_narasumber');
            $table->dropIndex('idx_broadcast_status');
            $table->dropIndex('idx_broadcast_started');
            $table->dropIndex('idx_broadcast_penyiar');
        });
    }
};
