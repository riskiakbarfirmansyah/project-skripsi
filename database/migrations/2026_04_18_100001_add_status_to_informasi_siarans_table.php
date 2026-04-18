<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('informasi_siarans', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'completed'])->default('pending')->after('keterangan');
            $table->timestamp('started_at')->nullable()->after('status');
            $table->timestamp('ended_at')->nullable()->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('informasi_siarans', function (Blueprint $table) {
            $table->dropColumn(['status', 'started_at', 'ended_at']);
        });
    }
};
