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
            $table->foreignId('user_id')->nullable()->after('siaranID')
                  ->constrained('users')->nullOnDelete();
        });

        Schema::table('narasumber_byphones', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('byphoneID')
                  ->constrained('users')->nullOnDelete();
        });

        Schema::table('narasumber_talkshows', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('talkshowID')
                  ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('informasi_siarans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('narasumber_byphones', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('narasumber_talkshows', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
