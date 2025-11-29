<?php

namespace Tests\Feature\Admin;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminReportManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user with proper role
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    }

    /**
     * Test Case 1: Delete non-existent report returns 404 with Vietnamese message
     */
    public function test_delete_non_existent_report_returns_404_with_vietnamese_message()
    {
        $response = $this->actingAs($this->admin)
            ->deleteJson('/admin/reports/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Không tìm thấy báo cáo này. Có thể đã bị xóa bởi người dùng khác.'
            ]);
    }

    /**
     * Test Case 2: Concurrent update detection (optimistic locking)
     */
    public function test_concurrent_update_detection()
    {
        $report = Report::factory()->create();
        $oldUpdatedAt = $report->updated_at->format('Y-m-d H:i:s');

        // Simulate another user updating the report
        $report->update(['status' => 'reviewing']);
        $report->refresh();

        // Try to update with old updated_at timestamp
        $response = $this->actingAs($this->admin)
            ->patch("/admin/reports/{$report->id}", [
                'status' => 'resolved',
                'admin_notes' => 'Test notes',
                'updated_at' => $oldUpdatedAt, // Old timestamp
            ]);

        $response->assertSessionHasErrors('concurrent_update');
        $this->assertStringContainsString(
            'Dữ liệu đã được cập nhật bởi người dùng khác',
            session('errors')->get('concurrent_update')[0]
        );
    }

    /**
     * Test Case 3: Invalid report ID returns 404
     */
    public function test_invalid_report_id_returns_404()
    {
        // Test with non-numeric ID (abc)
        $response = $this->actingAs($this->admin)
            ->get('/admin/reports/abc');

        $response->assertRedirect('/admin/reports');
        $response->assertSessionHas('error', 'Không tìm thấy báo cáo này. Có thể đã bị xóa.');

        // Test with non-numeric ID (214 - assuming this doesn't exist)
        $response = $this->actingAs($this->admin)
            ->get('/admin/reports/214');

        $response->assertRedirect('/admin/reports');
        $response->assertSessionHas('error', 'Không tìm thấy báo cáo này. Có thể đã bị xóa.');

        // Test with very large ID
        $response = $this->actingAs($this->admin)
            ->get('/admin/reports/99999999');

        $response->assertRedirect('/admin/reports');
        $response->assertSessionHas('error', 'Không tìm thấy báo cáo này. Có thể đã bị xóa.');
    }

    /**
     * Test Case 4: Update with invalid status fails validation
     */
    public function test_update_with_invalid_status_fails_validation()
    {
        $report = Report::factory()->create();

        $response = $this->actingAs($this->admin)
            ->patch("/admin/reports/{$report->id}", [
                'status' => 'invalid_status',
                'admin_notes' => 'Test notes',
            ]);

        $response->assertSessionHasErrors('status');
    }

    /**
     * Test Case 5: Admin notes exceeding max length fails validation
     */
    public function test_admin_notes_exceeding_max_length_fails_validation()
    {
        $report = Report::factory()->create();

        $longText = str_repeat('a', 1001); // 1001 characters

        $response = $this->actingAs($this->admin)
            ->patch("/admin/reports/{$report->id}", [
                'status' => 'resolved',
                'admin_notes' => $longText,
            ]);

        $response->assertSessionHasErrors('admin_notes');
        $this->assertStringContainsString(
            'Ghi chú quá dài',
            session('errors')->get('admin_notes')[0]
        );
    }

    /**
     * Test Case 6.1: Admin notes with only ASCII whitespace fails validation
     */
    public function test_admin_notes_with_only_whitespace_fails_validation()
    {
        $report = Report::factory()->create();

        $response = $this->actingAs($this->admin)
            ->patch("/admin/reports/{$report->id}", [
                'status' => 'resolved',
                'admin_notes' => '     ', // Only spaces
            ]);

        $response->assertSessionHasErrors('admin_notes');
        $this->assertStringContainsString(
            'không được chỉ chứa khoảng trắng',
            session('errors')->get('admin_notes')[0]
        );
    }

    /**
     * Test Case 6.2: Admin notes with full-width whitespace fails validation
     */
    public function test_admin_notes_with_fullwidth_whitespace_fails_validation()
    {
        $report = Report::factory()->create();

        $response = $this->actingAs($this->admin)
            ->patch("/admin/reports/{$report->id}", [
                'status' => 'resolved',
                'admin_notes' => '　　　', // Full-width spaces (U+3000)
            ]);

        $response->assertSessionHasErrors('admin_notes');
        $this->assertStringContainsString(
            'không được chỉ chứa khoảng trắng',
            session('errors')->get('admin_notes')[0]
        );
    }

    /**
     * Test Case 8: Status field validates enum values
     */
    public function test_status_field_validates_enum_values()
    {
        $report = Report::factory()->create();

        $validStatuses = ['pending', 'reviewing', 'resolved', 'dismissed'];

        foreach ($validStatuses as $status) {
            $response = $this->actingAs($this->admin)
                ->patch("/admin/reports/{$report->id}", [
                    'status' => $status,
                    'admin_notes' => 'Test',
                ]);

            $response->assertSessionHasNoErrors();
        }

        // Test invalid status
        $response = $this->actingAs($this->admin)
            ->patch("/admin/reports/{$report->id}", [
                'status' => 'invalid',
                'admin_notes' => 'Test',
            ]);

        $response->assertSessionHasErrors('status');
    }

    /**
     * Test Case 10: Invalid page parameter redirects to first page
     */
    public function test_invalid_page_parameter_redirects_to_first_page()
    {
        // Test with non-numeric page
        $response = $this->actingAs($this->admin)
            ->get('/admin/reports?page=abc');

        $response->assertRedirect('/admin/reports?page=1');
        $response->assertSessionHas('error', 'Trang không tồn tại. Đã chuyển về trang đầu tiên.');

        // Test with negative page
        $response = $this->actingAs($this->admin)
            ->get('/admin/reports?page=-1');

        $response->assertRedirect('/admin/reports?page=1');
    }

    /**
     * Test Case 10: Non-numeric page parameter redirects to first page
     */
    public function test_non_numeric_page_parameter_redirects_to_first_page()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/reports?page=abc123');

        $response->assertRedirect('/admin/reports?page=1');
        $response->assertSessionHas('error');
    }

    /**
     * Test successful report update
     */
    public function test_successful_report_update()
    {
        $report = Report::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->admin)
            ->patch("/admin/reports/{$report->id}", [
                'status' => 'resolved',
                'admin_notes' => 'Đã xử lý xong',
                'updated_at' => $report->updated_at->format('Y-m-d H:i:s'),
            ]);

        $response->assertSessionHas('success', 'Cập nhật báo cáo thành công!');

        $report->refresh();
        $this->assertEquals('resolved', $report->status);
        $this->assertEquals('Đã xử lý xong', $report->admin_notes);
    }

    /**
     * Test successful report deletion
     */
    public function test_successful_report_deletion()
    {
        $report = Report::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/admin/reports/{$report->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xóa báo cáo thành công!'
            ]);

        $this->assertSoftDeleted('reports', ['id' => $report->id]);
    }
}
