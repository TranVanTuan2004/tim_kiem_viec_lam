<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EmployerReport;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployerReport>
 */
class EmployerReportFactory extends Factory
{
    protected $model = EmployerReport::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'employer_id' => 1,
            'candidate_id' => 1,
            'type' => 'violation',
            'reason' => $this->faker->sentence(),
            'status' => 'pending',
            'reportable_type' => 'App\\Models\\JobPosting',
            'reportable_id' => 1,
        ];
    }
}
