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
        Schema::create('readers', function (Blueprint $table) {
            $table->id();
            $table->integer('points')->default(0);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('picture')->nullable();
            $table->text('bio')->nullable();
            $table->string('nickname')->nullable();
            $table->text('quote')->nullable();
            $table->integer('number_of_books')->default(0);
            $table->integer('number_of_challenges')->default(0);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('readers');
    }
};
