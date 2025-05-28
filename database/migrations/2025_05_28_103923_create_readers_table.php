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
            $table->integer('points');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('picture');
            $table->text('bio');
            $table->string('nickname');
            $table->text('quote');
            $table->integer('number_of_books');
            $table->integer('number_of_challenges');
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
