<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_of_the_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->date('date')->index();
            $table->enum('scope_type', ['general', 'course']);
            $table->foreignId('course_id')->nullable()
                ->constrained()->nullOnDelete();

            $table->timestamps();

            $table->unique(['date', 'scope_type', 'course_id'],
                'qotd_unique_per_scope_per_day'
            );

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_of_the_days');
    }
};
