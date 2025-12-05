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


    public function topUsers(Request $request)
    {
        $limit = $request->get('limit', 10);
        $users = $this->activityLogService->getTopActiveUsers($limit);

        return response()->json($users);
    }


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

        return response()->json([
            'data' => $logs,
            'count' => count($logs),
        ]);
    }

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
