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
        Schema::create('question_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('past_question_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('score')->default(0);

            $table->integer('total_attempted')->default(0);

            $table->integer('correct_answers')->default(0);

            $table->timestamp('started_at')->nullable();

            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_attempts');
    }
};
