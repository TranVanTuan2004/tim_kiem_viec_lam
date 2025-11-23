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
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('status');
            $table->text('admin_notes')->nullable()->after('reviewed_by');
            $table->timestamp('reviewed_at')->nullable()->after('admin_notes');

            $table->index('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['reviewed_by', 'admin_notes', 'reviewed_at']);
            $table->dropIndex(['reviewed_by']);
        });
    }
};
