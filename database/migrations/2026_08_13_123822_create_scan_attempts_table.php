<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scan_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('past_question_id')->nullable()
                ->constrained('past_questions')->nullOnDelete();
            $table->enum('status', ['pending', 'success', 'failed', 'rejected'])
                ->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->json('raw_ai_response')->nullable();
            $table->json('file_paths')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_attempts');
    }
};
