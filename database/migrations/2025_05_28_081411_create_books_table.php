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
            $table->json("title");
            $table->json("description")->nullable();
            $table->date("publish_date")->nullable();
            $table->string('book_pdf');
            $table->string('image_cover')->nullable();
            $table->integer('star_rate')->default(0);
            $table->integer('number_of_pages');
            $table->boolean('is_challenged')->default(false);
            $table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete()->nullable();;
            $table->foreignId('size_category_id')->constrained('size_categories')->cascadeOnDelete()->nullable();
            $table->timestamps();
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
