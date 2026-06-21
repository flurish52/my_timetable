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
        Schema::create('question_attempt_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('question_attempt_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('question_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('question_option_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->longText('answer_text')->nullable();

            $table->boolean('is_correct')->nullable();

            $table->integer('marks_awarded')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_attempt_answers');
    }
};
