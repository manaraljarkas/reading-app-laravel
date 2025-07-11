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
        Schema::create('book_challenges', function (Blueprint $table) {
            $table->id();
            $table->integer('duration');
            $table->integer('points');
            $table->json('description')->nullable();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_challenges');
    }
};
