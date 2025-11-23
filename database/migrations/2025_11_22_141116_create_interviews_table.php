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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained('users')->onDelete('cascade');
            
            // Interview Details
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['phone', 'video', 'in_person'])->default('video');
            $table->string('location')->nullable(); // For in-person or meeting link
            
            // Scheduling
            $table->dateTime('scheduled_at');
            $table->integer('duration')->default(60); // minutes
            $table->string('timezone')->default('Asia/Ho_Chi_Minh');
            
            // Status Management
            $table->enum('status', ['pending', 'confirmed', 'declined', 'rescheduled', 'completed', 'cancelled'])
                ->default('pending');
            $table->text('candidate_notes')->nullable();
            $table->text('employer_notes')->nullable();
            
            // Reschedule Proposals
            $table->json('proposed_times')->nullable(); // Array of candidate's proposed times
            
            // Calendar Integration
            $table->string('google_event_id')->nullable();
            $table->string('outlook_event_id')->nullable();
            
            // Reminders
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better query performance
            $table->index(['employer_id', 'scheduled_at']);
            $table->index(['candidate_id', 'scheduled_at']);
            $table->index('status');
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
