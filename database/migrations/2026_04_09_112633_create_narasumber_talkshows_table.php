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
        Schema::create('narasumber_talkshows', function (Blueprint $table) {
            $table->id('talkshowID');
            $table->string('nama');
            $table->string('instansi');
            $table->string('nomor_telp');
            $table->string('tema_wawancara');
            $table->dateTime('waktu_wawancara');
            $table->text('draft_pertanyaan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('narasumber_talkshows');
    }
};
