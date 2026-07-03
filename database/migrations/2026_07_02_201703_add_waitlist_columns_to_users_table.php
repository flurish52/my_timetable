<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('waitlist_school_id')->nullable()
                ->constrained('schools')->nullOnDelete();
            $table->timestamp('waitlist_joined_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('waitlist_school_id');
            $table->dropColumn('waitlist_joined_at');
        });
    }
};


