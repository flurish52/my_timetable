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
        Schema::create('question_sections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('past_question_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('instructions')->nullable();

            $table->integer('position')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_sections');
    }
};
