<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scan_attempts', function (Blueprint $t) {
            $t->enum('type', ['paper', 'notes'])->default('paper')->after('user_id');
            $t->json('options')->nullable()->after('file_paths');
        });

        Schema::table('past_questions', function (Blueprint $t) {
            $t->enum('kind', ['past_question', 'notes_quiz'])->default('past_question')->after('visibility');
        });

        Schema::table('questions', function (Blueprint $t) {
            $t->text('explanation')->nullable()->after('answer_confidence');
            $t->text('source_excerpt')->nullable()->after('explanation');
        });
    }

    public function down(): void
    {
        Schema::table('questions', fn (Blueprint $t) => $t->dropColumn(['explanation', 'source_excerpt']));
        Schema::table('past_questions', fn (Blueprint $t) => $t->dropColumn('kind'));
        Schema::table('scan_attempts', fn (Blueprint $t) => $t->dropColumn(['type', 'options']));
    }
};
