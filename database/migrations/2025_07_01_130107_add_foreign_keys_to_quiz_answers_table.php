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
        Schema::table('quiz_answers', function (Blueprint $table) {
            $table->foreign(['quiz_id'], 'quiz_answers_ibfk_1')->references(['id'])->on('quiz')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['player_id'], 'quiz_answers_ibfk_2')->references(['id'])->on('players')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['team_id'], 'quiz_answers_ibfk_3')->references(['id'])->on('teams')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['league_id'], 'quiz_answers_ibfk_4')->references(['id'])->on('leagues')->onUpdate('restrict')->onDelete('restrict');
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_answers', function (Blueprint $table) {
            $table->dropForeign('quiz_answers_ibfk_1');
            $table->dropForeign('quiz_answers_ibfk_2');
            $table->dropForeign('quiz_answers_ibfk_3');
            $table->dropForeign('quiz_answers_ibfk_4');
        });
    }
};
