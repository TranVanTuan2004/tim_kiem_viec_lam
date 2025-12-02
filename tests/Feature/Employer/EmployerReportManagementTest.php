<?php

namespace Tests\Feature\Employer;

use App\Models\CandidateProfile;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmployerReportManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $employer;
    protected $candidateProfile;

    protected function setUp(): void
    {
        parent::setUp();

        // Create employer user
        $this->employer = User::factory()->create();
        $this->employer->assignRole('Employer');

        // Create candidate profile to report
        $candidateUser = User::factory()->create();
        $candidateUser->assignRole('Candidate');
        $this->candidateProfile = CandidateProfile::factory()->create(['user_id' => $candidateUser->id]);
    }

    /**
     * Test Case 8.1: Invalid filter status value (modified via F12) in Index page
     * Verify "Danh mục không tồn tại" error message
     */
    public function test_filter_invalid_status_returns_error()
    {
        $response = $this->actingAs($this->employer)
            ->get('/employer/reports?status=invalid_status_from_f12');

        $response->assertRedirect('/employer/reports');
        $response->assertSessionHas('error', 'Danh mục không tồn tại.');
    }

    /**
     * Test Case 8.2: Store Report - Validation (Invalid Type - Select Option)
     * Verify that when user modified select option value in F12, error message shows "Danh mục không tồn tại"
     */
    public function test_store_report_invalid_type_select_option()
    {
        $response = $this->actingAs($this->employer)
            ->post('/employer/reports', [
                'candidate_id' => $this->candidateProfile->id,
                'type' => 'invalid_type_from_f12',
                'reason' => 'This is a valid reason for reporting.',
            ]);

        $response->assertSessionHasErrors('type');
        $this->assertStringContainsString(
            'Danh mục không tồn tại',
            session('errors')->get('type')[0]
        );
    }

    /**
     * Test successful report creation
     */
    public function test_store_report_success()
    {
        $response = $this->actingAs($this->employer)
            ->post('/employer/reports', [
                'candidate_id' => $this->candidateProfile->id,
                'type' => 'spam',
                'reason' => 'This is a valid reason for reporting.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reports', [
            'reporter_id' => $this->employer->id,
            'reportable_id' => $this->candidateProfile->id,
            'reportable_type' => CandidateProfile::class,
            'reason' => 'This is a valid reason for reporting.',
        ]);
    }
}
