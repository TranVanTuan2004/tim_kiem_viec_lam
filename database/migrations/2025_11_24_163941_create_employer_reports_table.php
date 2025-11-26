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
        Schema::create('employer_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employer_id'); 
            $table->unsignedBigInteger('candidate_id');
            $table->string('type'); 
            $table->text('reason');
            $table->string('status')->default('pending'); 
            $table->timestamps();

            $table->foreign('employer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('candidate_id')->references('id')->on('candidate_profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_reports');
    }
};
