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
        Schema::table('employer_reports', function (Blueprint $table) {
            $table->string('reportable_type')->nullable()->after('reason');
            $table->unsignedBigInteger('reportable_id')->nullable()->after('reportable_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employer_reports', function (Blueprint $table) {
            $table->dropColumn(['reportable_type', 'reportable_id']);
        });
    }
};
