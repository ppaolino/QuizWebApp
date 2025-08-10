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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('quiz_id')->index('quiz_id');
            $table->unsignedBigInteger('user_id')->nullable()->index('user_id');
            $table->integer('score')->nullable()->default(0);
            $table->dateTime('started_at')->nullable()->useCurrent();
            $table->dateTime('submitted_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
