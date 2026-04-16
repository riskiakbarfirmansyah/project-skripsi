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
            $table->boolean('is_self_registered')->default(false)->after('draft_pertanyaan');
        });

        Schema::table('narasumber_talkshows', function (Blueprint $table) {
            $table->boolean('is_self_registered')->default(false)->after('draft_pertanyaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('narasumber_byphones', function (Blueprint $table) {
            $table->dropColumn('is_self_registered');
        });

        Schema::table('narasumber_talkshows', function (Blueprint $table) {
            $table->dropColumn('is_self_registered');
        });
    }
};
