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
        Schema::create('pendaftars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete()->default(null);
            $table->string('nama_lengkap');
            $table->date('date_of_birth');
            $table->string('email')->nullable();
            $table->string('asal_instansi');
            $table->string('no_telepon');
            $table->string('riwayat_penyakit');
            $table->string('registrant_picture')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftars');
    }
};
