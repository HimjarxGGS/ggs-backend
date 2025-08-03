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
        Schema::create('pendaftar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approved_by')->constrained('users');
            $table->foreignId('event_id')->constrained('events');
            $table->foreignId('pendaftar_id')->constrained('pendaftars');
            $table->string('bukti_payment');
            $table->string('opsi_payment');
            $table->string('bukti_share');
            $table->enum('status', ['pending', 'verified'])->default('pending');
            $table->enum('kesediaan_hadir', ['ya', 'tidak'])->default('ya');
            $table->enum('kesediaan_menaati_aturan', ['ya', 'tidak'])->default('ya');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftar_events');
    }
};
