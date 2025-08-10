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
        Schema::create('user_answers', function (Blueprint $table) {
             $table->integer('user_answer_id', true);
            $table->integer('attempt_id')->index('attempt_id');
            $table->integer('player_id')->index('player_id');
            $table->boolean('is_correct')->nullable()->default(false);
            $table->dateTime('submitted_at')->nullable()->useCurrent();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
