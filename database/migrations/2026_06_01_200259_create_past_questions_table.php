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
        Schema::create('past_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')->constrained();
            $table->foreignId('course_id')->constrained();
            $table->foreignId('semester_id')->constrained();

            $table->string('session'); // 2024/2025

            $table->string('title');
            $table->string('slug')
                ->unique(); #from title

            $table->text('instructions')->nullable();
            $table->text('description')->nullable();

            $table->integer('duration_minutes')->nullable();

            $table->string('source_file')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('past_questions');
    }
};
