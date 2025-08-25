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
        Schema::table('reader_books', function (Blueprint $table) {
            $table->dropColumn('earned_points');
        });

        Schema::table('reader_challenges', function (Blueprint $table) {
            $table->dropColumn('earned_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reader_books', function (Blueprint $table) {
            $table->integer('earned_points')->default(0);
        });

        Schema::table('reader_challenges', function (Blueprint $table) {
            $table->integer('earned_points')->default(0);
        });
    }
};
