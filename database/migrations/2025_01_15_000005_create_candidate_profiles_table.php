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
        Schema::create('candidate_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('province');
            $table->string('cv_file')->nullable();
            $table->text('summary')->nullable();
            $table->string('current_position')->nullable();
            $table->string('current_company')->nullable();
            $table->decimal('expected_salary', 10, 2)->nullable();
            $table->enum('experience_level', ['fresher', 'junior', 'middle', 'senior', 'lead'])->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_available', 'city']);
            $table->index(['experience_level', 'expected_salary']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_profiles');
    }
};
