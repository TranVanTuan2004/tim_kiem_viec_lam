<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Display activity logs index page
     */
    public function index(Request $request): Response
    {
        $filters = [
            'user_id' => $request->get('user_id'),
            'type' => $request->get('type'),
            'event' => $request->get('event'),
            'search' => $request->get('search'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $logs = $this->activityLogService->getLogs($filters);
        
        // Transform logs để đảm bảo causer data được serialize đúng
        $logs->getCollection()->transform(function ($log) {
            return [
                'id' => $log->id,
                'log_name' => $log->log_name,
                'description' => $log->description,
                'subject_type' => $log->subject_type,
                'subject_id' => $log->subject_id,
                'event' => $log->event,
                'properties' => $log->properties,
                'created_at' => $log->created_at->toISOString(),
                'causer' => $log->causer ? [
                    'id' => $log->causer->id,
                    'name' => $log->causer->name,
                    'email' => $log->causer->email,
                    'avatar' => $log->causer->avatar,
                ] : null,
            ];
        });
        
        $statistics = $this->activityLogService->getStatistics($filters);
        $recentActivities = $this->activityLogService->getRecentActivities(10);

        return Inertia::render('admin/activity-logs/Index', [
            'logs' => $logs,
            'statistics' => $statistics,
            'recentActivities' => $recentActivities,
            'filters' => $filters,
        ]);
    }

    /**
     * Get activity statistics (API endpoint)
     */
    public function statistics(Request $request)
    {
        $filters = [
            'user_id' => $request->get('user_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $statistics = $this->activityLogService->getStatistics($filters);

        return response()->json($statistics);
    }

    /**
     * Get recent activities (API endpoint)
     */
    public function recent(Request $request)
    {
        $limit = $request->get('limit', 10);
        $filters = [
            'user_id' => $request->get('user_id'),
            'type' => $request->get('type'),
        ];

        $activities = $this->activityLogService->getRecentActivities($limit, $filters);

        return response()->json($activities);
    }

    /**
     * Get top active users (API endpoint)
     */
    public function topUsers(Request $request)
    {
        $limit = $request->get('limit', 10);
        $users = $this->activityLogService->getTopActiveUsers($limit);

        return response()->json($users);
    }

    /**
     * Export activity logs
     */
    public function export(Request $request)
    {
        $filters = [
            'user_id' => $request->get('user_id'),
            'type' => $request->get('type'),
            'event' => $request->get('event'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $logs = $this->activityLogService->exportLogs($filters);

        // In real application, you would generate CSV or Excel file
        return response()->json([
            'data' => $logs,
            'count' => count($logs),
        ]);
    }

    /**
     * Clean old logs
     */
    public function clean(Request $request)
    {
        $days = $request->get('days', 90);
        $deleted = $this->activityLogService->cleanOldLogs($days);

        return response()->json([
            'message' => "Đã xóa {$deleted} bản ghi cũ hơn {$days} ngày",
            'deleted_count' => $deleted,
        ]);
    }
}
