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
        Schema::create('reader_books', function (Blueprint $table) {
            $table->id();
            $table->integer('progress')->default(0);
            $table->enum('status', ['to_read', 'in_read', 'completed'])->default('in_read');
            $table->boolean('is_favourite')->default(false);
            $table->foreignId('reader_id')->constrained('readers')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reader_books');
    }
};
