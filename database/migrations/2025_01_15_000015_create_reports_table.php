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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporter_id');
            $table->morphs('reportable'); // reportable_id, reportable_type (job, company, user) - đã có index tự động
            $table->enum('type', ['spam', 'fraud', 'inappropriate', 'fake', 'other']);
            $table->text('reason');
            $table->enum('status', ['pending', 'reviewing', 'resolved', 'dismissed'])->default('pending');
            $table->timestamps();
            
            // morphs() đã tự tạo index cho reportable_type + reportable_id rồi, không cần thêm
            $table->index(['status', 'type']);
            $table->index('reporter_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
