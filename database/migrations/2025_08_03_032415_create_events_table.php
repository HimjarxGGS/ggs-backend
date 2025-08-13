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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->date('event_date');
            $table->enum('status', ['active', 'finished'])->default('active');
            $table->enum('event_format', ['online', 'offline'])->default('offline');
            $table->string('location');
            // $table->string('poster');
            $table->string('poster')->nullable();
            $table->enum('need_registrant_picture', ['ya', 'tidak'])->default('tidak');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
