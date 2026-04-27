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
        Schema::table('narasumber_byphones', function (Blueprint $table) {
            $table->string('byphone_schedule')->nullable()->after('waktu_wawancara');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('narasumber_byphones', function (Blueprint $table) {
            $table->dropColumn('byphone_schedule');
        });
    }
};
