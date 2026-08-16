<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the old unique index first — we're replacing it with one that
        // includes user_id, and MySQL won't let us alter the enum + add a
        // column while an index referencing the changing columns is in a
        // state that conflicts with the new constraint.
        Schema::table('question_of_the_days', function (Blueprint $table) {
            $table->dropUnique('qotd_unique_per_scope_per_day');
        });

        Schema::table('question_of_the_days', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('course_id')
                ->constrained()->cascadeOnDelete();
        });

        // Widen the enum to include 'personal'. Laravel doesn't have a clean
        // schema-builder method for altering an existing enum, so this uses
        // raw SQL — adjust the column list if you're not on MySQL/MariaDB.
        DB::statement("ALTER TABLE question_of_the_days MODIFY scope_type ENUM('general', 'course', 'personal') NOT NULL");

        Schema::table('question_of_the_days', function (Blueprint $table) {
            $table->unique(['date', 'scope_type', 'course_id', 'user_id'],
                'qotd_unique_per_scope_per_day'
            );
        });
    }

    public function down(): void
    {
        Schema::table('question_of_the_days', function (Blueprint $table) {
            $table->dropUnique('qotd_unique_per_scope_per_day');
            $table->dropConstrainedForeignId('user_id');
        });

        DB::statement("ALTER TABLE question_of_the_days MODIFY scope_type ENUM('general', 'course') NOT NULL");

        Schema::table('question_of_the_days', function (Blueprint $table) {
            $table->unique(['date', 'scope_type', 'course_id'],
                'qotd_unique_per_scope_per_day'
            );
        });
    }
};
