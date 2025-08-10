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
        Schema::table('user_answers', function (Blueprint $table) {
            $table->foreign(['attempt_id'], 'user_answers_ibfk_1')->references(['id'])->on('quiz_attempts')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['player_id'], 'user_answers_ibfk_2')->references(['id'])->on('players')->onUpdate('restrict')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_answers', function (Blueprint $table) {
             $table->dropForeign('user_answers_ibfk_1');
            $table->dropForeign('user_answers_ibfk_2');

        });
    }
};
