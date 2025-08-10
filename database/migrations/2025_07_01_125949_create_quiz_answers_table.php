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
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->integer('answer_id', true);
            $table->integer('quiz_id')->index('idx_quiz_answers_quiz_id');
            $table->integer('player_id')->index('idx_quiz_answers_player_id');
            $table->text('context_info')->nullable();
            $table->integer('team_id')->nullable()->index('team_id');
            $table->integer('league_id')->nullable()->index('league_id');

            $table->unique(['quiz_id', 'player_id'], 'idx_quiz_answers_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
    }
};
