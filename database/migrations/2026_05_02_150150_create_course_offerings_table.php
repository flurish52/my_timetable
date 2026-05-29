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
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('programme_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('level_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', ['core', 'elective']);

            $table->boolean('is_general')->default(false);

            $table->timestamps();

            $table->unique(['course_id', 'programme_id', 'level_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_offerings');
    }
};
