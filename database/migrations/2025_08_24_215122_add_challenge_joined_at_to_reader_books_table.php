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
            $table->timestamp('challenge_joined_at')->nullable()->after('is_challenged');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reader_books', function (Blueprint $table) {
            $table->dropColumn('challenge_joined_at');
        });
    }
};
