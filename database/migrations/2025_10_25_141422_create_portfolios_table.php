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
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidate_profiles')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('project_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('demo_url')->nullable();
            $table->json('images')->nullable(); // Store multiple project images
            $table->json('technologies')->nullable(); // Store array of technologies used
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_ongoing')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('display_order')->default(0);
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
