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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('img');
            $table->string('slug');
            $table->string('tag');
            $table->string('author');
            $table->dateTime('published_at')->nullable();
            $table->foreignId('pic')->nullable()->constrained('users')->reference('id')->on('users')->nullOnDelete();
            $table->enum('status', ['draft', 'reviewing', 'published'])->default('published');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
