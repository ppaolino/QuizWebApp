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
        Schema::create('quiz', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100);
            $table->text('prompt_text');
            $table->integer('max_errors')->nullable()->default(0);
            $table->unsignedBigInteger('created_by')->index('created_by');
            $table->boolean('is_published')->nullable()->default(false);
            $table->unsignedBigInteger('approved_by')->index('approved_by');
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('published_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz');
    }
};
