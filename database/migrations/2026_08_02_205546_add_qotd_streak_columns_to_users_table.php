<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('qotd_current_streak')->default(0);
            $table->unsignedInteger('qotd_longest_streak')->default(0);
            $table->date('qotd_last_attempted_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['qotd_current_streak', 'qotd_longest_streak', 'qotd_last_attempted_date']);
        });
    }
};
