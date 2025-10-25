<?php

namespace Database\Seeders;

use App\Models\CandidateProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class CandidateProfileSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get all users with 'Candidate' role who don't have a profile yet
        $candidates = User::role('Candidate')
            ->whereDoesntHave('candidateProfile')
            ->get();

        // ✅ Sử dụng Factory - Sạch và dễ maintain hơn!
        foreach ($candidates as $user) {
            CandidateProfile::factory()->create([
                'user_id' => $user->id, // Override user_id
            ]);
        }

        $this->command->info('Candidate profiles created successfully!');
    }
}

