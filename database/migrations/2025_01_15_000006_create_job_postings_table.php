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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('requirements');
            $table->text('benefits')->nullable();
            $table->unsignedBigInteger('industry_id');
            $table->string('location');
            $table->string('city');
            $table->string('province');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship', 'freelance']);
            $table->enum('experience_level', ['fresher', 'junior', 'middle', 'senior', 'lead']);
            $table->decimal('min_salary', 10, 2)->nullable();
            $table->decimal('max_salary', 10, 2)->nullable();
            $table->enum('salary_type', ['monthly', 'yearly', 'hourly', 'project'])->default('monthly');
            $table->date('application_deadline')->nullable();
            $table->integer('quantity')->default(1);
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'closed'])->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->integer('views')->default(0);
            $table->integer('applications_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('published_at')->nullable();
            
            $table->index(['status', 'published_at']);
            $table->index(['city', 'province']);
            $table->index(['industry_id', 'experience_level']);
            $table->index(['min_salary', 'max_salary']);
            $table->index(['is_featured', 'status']);
            $table->index('company_id');
            $table->index('industry_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
