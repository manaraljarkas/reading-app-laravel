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
        Schema::table('reader_challenges', function (Blueprint $table) {
            $table->integer('completed_books')->default(0)->after('percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reader_challenges', function (Blueprint $table) {
            $table->dropColumn('completed_books');
        });
    }
};
