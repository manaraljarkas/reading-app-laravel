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
        Schema::table('books', function (Blueprint $table) {
            $table->integer('points')->default(0);
        });

        Schema::table('reader_books', function (Blueprint $table) {
            $table->integer('earned_points')->default(0);
        });

        Schema::table('reader_challenges', function (Blueprint $table) {
            $table->integer('earned_points')->default(0);
        });

        Schema::table('readers', function (Blueprint $table) {
            $table->integer('total_points')->default(0);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books_and_reader_tables', function (Blueprint $table) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('points');
            });

            Schema::table('reader_books', function (Blueprint $table) {
                $table->dropColumn('earned_points');
            });

            Schema::table('reader_challenges', function (Blueprint $table) {
                $table->dropColumn('earned_points');
            });

            Schema::table('readers', function (Blueprint $table) {
                $table->dropColumn('total_points');
            });
        });
    }
};
