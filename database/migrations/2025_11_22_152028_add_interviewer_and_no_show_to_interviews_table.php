<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            // Add interviewer field (HR or Hiring Manager)
            $table->foreignId('interviewer_id')->nullable()->after('employer_id')->constrained('users')->onDelete('set null');
            $table->string('interviewer_role')->nullable()->after('interviewer_id'); // HR, Hiring Manager, Technical Lead, etc.
        });

        // Update status enum to include 'no_show'
        DB::statement("ALTER TABLE interviews MODIFY COLUMN status ENUM('pending', 'confirmed', 'declined', 'rescheduled', 'completed', 'cancelled', 'no_show') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropForeign(['interviewer_id']);
            $table->dropColumn(['interviewer_id', 'interviewer_role']);
        });

        // Revert status enum
        DB::statement("ALTER TABLE interviews MODIFY COLUMN status ENUM('pending', 'confirmed', 'declined', 'rescheduled', 'completed', 'cancelled') DEFAULT 'pending'");
    }
};
