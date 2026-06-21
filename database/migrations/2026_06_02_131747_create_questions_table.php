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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('past_question_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('question_section_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('question_group_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('parent_question_id')
                ->nullable()
                ->constrained('questions')
                ->nullOnDelete();

            $table->enum('question_type', [
                'objective',
                'true_false',
                'fill_blank',
                'short_answer',
                'essay'
            ]);

            $table->longText('question_text');

            $table->integer('marks')->default(1);

            $table->integer('position')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
