<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewReportCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $report;

    public function __construct(Report $report)
    {
        $this->report = $report;

        Log::info('NewReportCreated event được gọi', [
            'report_id' => $report->id,
            'reason'    => $report->reason,
        ]);
    }

    public function broadcastOn()
    {
        return new Channel('admin-reports');
    }
    
    public function broadcastAs()
    {
        return 'NewReportCreated';
    }

    public function broadcastWith()
    {
        return [
            'report' => [
                'id' => $this->report->id,
                'user' => [
                    'id' => $this->report->reporter?->id,
                    'name' => $this->report->reporter?->name,
                ],
                'reason' => $this->report->reason,
                'status' => $this->report->status,
                'status_label' => $this->report->getStatusLabel(),
                'reportable_type' => $this->report->reportable_type,
                'reportable_type_label' => $this->report->getReportableTypeLabel(),
                'reportable' => $this->report->reportable ? [
                    'id' => $this->report->reportable->id,
                    'title' => $this->report->reportable->title ?? $this->report->reportable->company_name ?? 'Không xác định',
                    'slug' => $this->report->reportable->slug ?? $this->report->reportable->company_slug ?? '#',
                ] : null,
                'created_at' => $this->report->created_at->format('Y-m-d H:i:s'),
            ],
        ];
    }
}