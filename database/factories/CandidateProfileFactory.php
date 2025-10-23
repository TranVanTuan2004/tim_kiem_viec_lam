<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CandidateProfile>
 */
class CandidateProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Tự động tạo user nếu chưa có
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'address' => fake()->address(),
            'city' => fake()->randomElement(['Hà Nội', 'TP. Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ']),
            'province' => fake()->randomElement(['Hà Nội', 'TP. Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ']),
            'summary' => fake()->paragraph(3),
            'current_position' => fake()->randomElement([
                'Software Engineer',
                'Frontend Developer',
                'Backend Developer',
                'Full Stack Developer',
                'Mobile Developer',
                'DevOps Engineer',
                'Data Analyst',
                'UI/UX Designer',
            ]),
            'expected_salary' => fake()->numberBetween(500, 3000),
            'experience_level' => fake()->randomElement(['fresher', 'junior', 'mid', 'senior']),
            'is_available' => true,
            'cv_file' => 'cvs/sample-cv.pdf',
        ];
    }
}
