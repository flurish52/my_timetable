<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing FK so we can relax the NOT NULL constraint.
        Schema::table('past_questions', function (Blueprint $table) {
            $table->dropForeign('past_questions_course_id_foreign');
        });

        DB::statement('ALTER TABLE past_questions MODIFY course_id BIGINT UNSIGNED NULL');

        Schema::table('past_questions', function (Blueprint $table) {
            $table->foreign('course_id')
                ->references('id')->on('courses')
                ->onDelete('set null')
                ->onUpdate('restrict');

            $table->string('raw_course_label')->nullable()->after('course_id');
        });
    }

    public function down(): void
    {
        Schema::table('past_questions', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('raw_course_label');
        });

        DB::statement('ALTER TABLE past_questions MODIFY course_id BIGINT UNSIGNED NOT NULL');

        Schema::table('past_questions', function (Blueprint $table) {
            $table->foreign('course_id')
                ->references('id')->on('courses')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
    }
};
