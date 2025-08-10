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
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->foreign(['quiz_id'], 'quiz_attempts_ibfk_1')->references(['id'])->on('quiz')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'], 'quiz_attempts_ibfk_2')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropForeign('quiz_attempts_ibfk_1');
            $table->dropForeign('quiz_attempts_ibfk_2');

        });
    }
};
