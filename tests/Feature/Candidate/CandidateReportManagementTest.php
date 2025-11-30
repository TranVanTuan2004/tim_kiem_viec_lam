<?php

namespace Tests\Feature\Candidate;

use App\Models\Company;
use App\Models\JobPosting;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CandidateReportManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $candidate;
    protected $jobPosting;

    protected function setUp(): void
    {
        parent::setUp();

        // Create candidate user
        $this->candidate = User::factory()->create();
        $this->candidate->assignRole('Candidate');

        // Create a job posting to report
        $company = Company::factory()->create();
        $this->jobPosting = JobPosting::factory()->create(['company_id' => $company->id]);
    }

    /**
     * Test Case 1: Search functionality
     */
    public function test_search_functionality()
    {
        // Create reports with specific reasons
        Report::factory()->create([
            'reporter_id' => $this->candidate->id,
            'reason' => 'UniqueReasonForSearch',
            'reportable_type' => 'App\Models\JobPosting',
            'reportable_id' => $this->jobPosting->id,
        ]);

        Report::factory()->create([
            'reporter_id' => $this->candidate->id,
            'reason' => 'OtherReason',
            'reportable_type' => 'App\Models\JobPosting',
            'reportable_id' => $this->jobPosting->id,
        ]);

        $response = $this->actingAs($this->candidate)
            ->get('/candidate/reports?search=UniqueReason');

        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) => $page
                ->has('reports.data', 1)
                ->where('reports.data.0.reason', 'UniqueReasonForSearch')
        );
    }

    /**
     * Test Case 2: Filter functionality
     */
    public function test_filter_functionality()
    {
        Report::factory()->create([
            'reporter_id' => $this->candidate->id,
            'status' => 'pending',
            'reportable_type' => 'App\Models\JobPosting',
            'reportable_id' => $this->jobPosting->id,
        ]);

        Report::factory()->create([
            'reporter_id' => $this->candidate->id,
            'status' => 'resolved',
            'reportable_type' => 'App\Models\JobPosting',
            'reportable_id' => $this->jobPosting->id,
        ]);

        $response = $this->actingAs($this->candidate)
            ->get('/candidate/reports?status=pending');

        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) => $page
                ->has('reports.data', 1)
                ->where('reports.data.0.status', 'pending')
        );
    }

    /**
     * Test Case 2.1: Invalid filter status value (modified via F12)
     * Verify "Danh mục không tồn tại" error message
     */
    public function test_filter_invalid_status_returns_error()
    {
        $response = $this->actingAs($this->candidate)
            ->get('/candidate/reports?status=invalid_status_from_f12');

        $response->assertRedirect('/candidate/reports');
        $response->assertSessionHas('error', 'Danh mục không tồn tại.');
    }

    /**
     * Test Case 3: Show non-existent report returns redirect with error
     */
    public function test_show_non_existent_report_returns_redirect()
    {
        $response = $this->actingAs($this->candidate)
            ->get('/candidate/reports/99999');

        $response->assertRedirect('/candidate/reports');
        $response->assertSessionHas('error', 'Không tìm thấy trang.');
    }

    /**
     * Test Case 4: Show invalid ID (non-numeric) returns redirect with error
     */
    public function test_show_invalid_id_returns_redirect()
    {
        // Test with non-numeric ID (abc)
        $response = $this->actingAs($this->candidate)
            ->get('/candidate/reports/abc');

        $response->assertRedirect('/candidate/reports');
        $response->assertSessionHas('error', 'Không tìm thấy trang.');

        // Test with mixed alphanumeric (grgr, g35h)
        $response = $this->actingAs($this->candidate)
            ->get('/candidate/reports/g35h');

        $response->assertRedirect('/candidate/reports');
        $response->assertSessionHas('error', 'Không tìm thấy trang.');

        // Test with very large ID (99999999999)
        $response = $this->actingAs($this->candidate)
            ->get('/candidate/reports/99999999999');

        $response->assertRedirect('/candidate/reports');
        $response->assertSessionHas('error', 'Không tìm thấy trang.');
    }

    /**
     * Test Case 5: Invalid Page Parameter redirects to first page
     */
    public function test_invalid_page_parameter_redirects_to_first_page()
    {
        // Test with non-numeric page (abc)
        $response = $this->actingAs($this->candidate)
            ->get('/candidate/reports?page=abc');

        $response->assertRedirect('/candidate/reports?page=1');
        $response->assertSessionHas('error', 'Trang không tồn tại. Đã chuyển về trang đầu tiên.');

        // Test with mixed alphanumeric (gm, 00f)
        $response = $this->actingAs($this->candidate)
            ->get('/candidate/reports?page=00f');

        $response->assertRedirect('/candidate/reports?page=1');
        $response->assertSessionHas('error', 'Trang không tồn tại. Đã chuyển về trang đầu tiên.');

        // Test with negative page
        $response = $this->actingAs($this->candidate)
            ->get('/candidate/reports?page=-1');

        $response->assertRedirect('/candidate/reports?page=1');
        $response->assertSessionHas('error', 'Trang không tồn tại. Đã chuyển về trang đầu tiên.');
    }

    /**
     * Test Case 6: Store Report - Success
     */
    public function test_store_report_success()
    {
        $response = $this->actingAs($this->candidate)
            ->post('/candidate/reports', [
                'reportable_type' => 'App\Models\JobPosting',
                'reportable_id' => $this->jobPosting->id,
                'type' => 'spam',
                'reason' => 'This is a valid reason for reporting.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reports', [
            'reporter_id' => $this->candidate->id,
            'reason' => 'This is a valid reason for reporting.',
        ]);
    }

    /**
     * Test Case 7: Store Report - Validation (Reason Max Length)
     */
    public function test_store_report_reason_max_length()
    {
        $longReason = str_repeat('a', 1001);

        $response = $this->actingAs($this->candidate)
            ->post('/candidate/reports', [
                'reportable_type' => 'App\Models\JobPosting',
                'reportable_id' => $this->jobPosting->id,
                'type' => 'spam',
                'reason' => $longReason,
            ]);

        $response->assertSessionHasErrors('reason');
    }

    /**
     * Test Case 7: Store Report - Validation (Reason Whitespace)
     */
    public function test_store_report_reason_whitespace()
    {
        $response = $this->actingAs($this->candidate)
            ->post('/candidate/reports', [
                'reportable_type' => 'App\Models\JobPosting',
                'reportable_id' => $this->jobPosting->id,
                'type' => 'spam',
                'reason' => '          ',
            ]);

        $response->assertSessionHasErrors('reason');
    }

    /**
     * Test Case 8: Store Report - Validation (Invalid Type - Select Option)
     * Verify that when user modified select option value in F12, error message shows "Danh mục không tồn tại"
     */
    public function test_store_report_invalid_type_select_option()
    {
        $response = $this->actingAs($this->candidate)
            ->post('/candidate/reports', [
                'reportable_type' => 'App\Models\JobPosting',
                'reportable_id' => $this->jobPosting->id,
                'type' => 'invalid_type_from_f12',
                'reason' => 'Valid reason with enough characters',
            ]);

        $response->assertSessionHasErrors('type');
        $this->assertStringContainsString(
            'Danh mục không tồn tại',
            session('errors')->get('type')[0]
        );
    }

    /**
     * Test Case 9: Store Report - Validation (Invalid Reportable Type - Select Option)
     */
    public function test_store_report_invalid_reportable_type()
    {
        $response = $this->actingAs($this->candidate)
            ->post('/candidate/reports', [
                'reportable_type' => 'App\Models\InvalidModel',
                'reportable_id' => $this->jobPosting->id,
                'type' => 'spam',
                'reason' => 'Valid reason with enough characters',
            ]);

        $response->assertSessionHasErrors('reportable_type');
        $this->assertStringContainsString(
            'Danh mục không tồn tại',
            session('errors')->get('reportable_type')[0]
        );
    }

    /**
     * Test Case 10: Store Report - Validation (Invalid Reportable)
     */
    public function test_store_report_invalid_reportable()
    {
        $response = $this->actingAs($this->candidate)
            ->post('/candidate/reports', [
                'reportable_type' => 'App\Models\JobPosting',
                'reportable_id' => 99999, // Non-existent ID
                'type' => 'spam',
                'reason' => 'Valid reason',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Không tìm thấy đối tượng báo cáo.');
    }
}
