<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('topic_tag')->nullable()->after('question_text');
            $table->enum('answer_source', ['human', 'ai_generated'])
                ->default('human')
                ->after('marks');
            $table->enum('answer_confidence', ['high', 'medium', 'low'])
                ->nullable()
                ->after('answer_source');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['topic_tag', 'answer_source', 'answer_confidence']);
        });
    }
};
