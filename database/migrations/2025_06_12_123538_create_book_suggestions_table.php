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
        Schema::create('book_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author_name')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('reader_id')->constrained('readers')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_suggestions');
    }
};
