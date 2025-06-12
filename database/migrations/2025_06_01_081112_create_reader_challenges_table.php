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
        Schema::create('reader_challenges', function (Blueprint $table) {
            $table->id();
            $table->enum('progress', ['in_progress', 'completed', 'failed'])->default('in_progress');
            $table->double('percentage');
            $table->foreignId('challenge_id')->constrained('challenges')->cascadeOnDelete();
            $table->foreignId('reader_id')->constrained('readers')->cascadeOnDelete();
            $table->index(['reader_id', 'challenge_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reader_challenges');
    }
};
