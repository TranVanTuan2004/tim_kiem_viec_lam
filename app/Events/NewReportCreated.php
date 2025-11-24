<?php
namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class NewReportCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function broadcastOn()
    {
        return new Channel('admin-reports');
    }

    public function broadcastWith()
    {
        return [
            'report' => [
                'id' => $this->report->id,
                'user' => [
                    'id' => $this->report->employer?->id,
                    'name' => $this->report->employer?->name,
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