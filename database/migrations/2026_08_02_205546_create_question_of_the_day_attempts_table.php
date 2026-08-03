<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_of_the_day_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_of_the_day_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->foreignId('selected_option_id')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->boolean('shared')->default(false);
            $table->boolean('shared_with_answer')->nullable();
            $table->timestamp('attempted_at');
            $table->timestamps();

            $table->unique(['question_of_the_day_id', 'user_id'], 'qotd_one_attempt_per_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_of_the_day_attempts');
    }
};
