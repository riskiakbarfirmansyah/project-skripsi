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
        Schema::create('broadcast_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyiar_id')->constrained('users')->cascadeOnDelete();
            $table->enum('narasumber_type', ['byphone', 'talkshow']);
            $table->unsignedBigInteger('narasumber_id');
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->enum('status', ['pending', 'active', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcast_sessions');
    }
};
