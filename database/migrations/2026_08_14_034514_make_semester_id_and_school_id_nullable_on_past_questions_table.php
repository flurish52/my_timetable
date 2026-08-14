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
        Schema::table('past_questions', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->unsignedBigInteger('semester_id')->nullable()->change();
            $table->foreign('semester_id')->references('id')->on('semesters')->nullOnDelete();

            $table->dropForeign(['school_id']);
            $table->unsignedBigInteger('school_id')->nullable()->change();
            $table->foreign('school_id')->references('id')->on('schools')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('past_questions', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->unsignedBigInteger('semester_id')->nullable(false)->change();
            $table->foreign('semester_id')->references('id')->on('semesters')->restrictOnDelete();

            $table->dropForeign(['school_id']);
            $table->unsignedBigInteger('school_id')->nullable(false)->change();
            $table->foreign('school_id')->references('id')->on('schools')->restrictOnDelete();
        });
    }
};
