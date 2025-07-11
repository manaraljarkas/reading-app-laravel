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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('description');
            $table->date('publish_date');
            $table->string('book_pdf');
            $table->string('cover_image');
            $table->integer('number_of_pages');
            $table->json('summary')->nullable();
            $table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete()->nullable();;
            $table->foreignId('size_category_id')->constrained('size_categories')->cascadeOnDelete()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
