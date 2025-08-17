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
            $table->foreignId('approved_by')->nullable()->default(null)->constrained('users')->nullOnDelete();
            $table->foreignId('event_id')->constrained('events');
            $table->foreignId('pendaftar_id')->constrained('pendaftars');
            $table->string('status')->default('pending');
            $table->string('bukti_payment')->nullable();
            $table->string('opsi_payment')->nullable();
            $table->string('bukti_share')->nullable();
            $table->enum('kesediaan_hadir', ['ya', 'tidak'])->default('ya');
            $table->enum('kesediaan_menaati_aturan', ['ya', 'tidak'])->default('ya');
            $table->timestamps();
            $table->softDeletes();
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
