<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityLogService
{
    public function getLogs(array $filters = [], int $perPage = 20)
    {
        $query = ActivityLog::with(['causer:id,name,email,avatar', 'subject']);

        if (isset($filters['user_id'])) {
            $query->byUser($filters['user_id']);
        }

        if (isset($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (isset($filters['event'])) {
            $query->byEvent($filters['event']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['search'])) {
            $query->where('description', 'like', '%' . $filters['search'] . '%');
        }

        return $query->latest()->paginate($perPage);
    }

    public function getStatistics(array $filters = []): array
    {
        $query = ActivityLog::query();

        if (isset($filters['user_id'])) {
            $query->where('causer_id', $filters['user_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return [
            'total' => $query->count(),
            'created' => (clone $query)->byEvent('created')->count(),
            'updated' => (clone $query)->byEvent('updated')->count(),
            'deleted' => (clone $query)->byEvent('deleted')->count(),
            'by_type' => (clone $query)
                ->select('log_name', DB::raw('count(*) as count'))
                ->groupBy('log_name')
                ->get()
                ->pluck('count', 'log_name'),
            'by_user' => (clone $query)
                ->select('causer_id', DB::raw('count(*) as count'))
                ->whereNotNull('causer_id')
                ->groupBy('causer_id')
            ->with('causer:id,name,email,avatar')
            ->get(),
            'by_day' => (clone $query)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get(),
        ];
    }


    public function getRecentActivities(int $limit = 10, array $filters = [])
    {
        $query = ActivityLog::with(['causer:id,name,email,avatar', 'subject']);

        if (isset($filters['user_id'])) {
            $query->byUser($filters['user_id']);
        }

        if (isset($filters['type'])) {
            $query->byType($filters['type']);
        }

        return $query->recent(7)->latest()->limit($limit)->get();
    }


    public function getTopActiveUsers(int $limit = 10): array
    {
        return ActivityLog::select('causer_id', DB::raw('count(*) as activity_count'))
            ->whereNotNull('causer_id')
            ->groupBy('causer_id')
            ->orderBy('activity_count', 'desc')
            ->limit($limit)
            ->with('causer:id,name,email,avatar')
            ->get()
            ->map(function ($log) {
                return [
                    'user' => $log->causer,
                    'activity_count' => $log->activity_count,
                ];
            })
            ->toArray();
    }


    public function cleanOldLogs(int $days = 90): int
    {
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();
        
        return $deleted;
    }


    public function exportLogs(array $filters = []): array
    {
        $logs = $this->getLogs($filters, 1000);
        
        return $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'user' => $log->causer ? $log->causer->name : 'System',
                'description' => $log->description,
                'subject_type' => class_basename($log->subject_type ?? ''),
                'event' => $log->event,
                'properties' => $log->properties,
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }

 
    public function getActivityTimeline(int $days = 7): array
    {
        $activities = ActivityLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date', 'hour')
            ->orderBy('date')
            ->orderBy('hour')
            ->get();

        return $activities->map(function ($activity) {
            return [
                'date' => $activity->date,
                'hour' => $activity->hour,
                'count' => $activity->count,
            ];
        })->toArray();
    }
}
