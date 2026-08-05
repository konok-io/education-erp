<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Models\CRM\CrmCampaign;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CampaignService
{
    public function getCampaigns(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = CrmCampaign::with(['creator']);

        if (!empty($filters['campaign_type'])) {
            $query->where('campaign_type', $filters['campaign_type']);
        }

        if (!empty($filters['channel'])) {
            $query->where('channel', $filters['channel']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('start_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('end_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createCampaign(array $data): CrmCampaign
    {
        return CrmCampaign::create([
            'uuid' => (string) Str::uuid(),
            'campaign_no' => CrmCampaign::generateCampaignNo(),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'campaign_type' => $data['campaign_type'],
            'channel' => $data['channel'],
            'status' => CrmCampaign::STATUS_DRAFT,
            'created_by' => $data['created_by'],
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'target_audience' => $data['target_audience'] ?? null,
            'audience_filters' => $data['audience_filters'] ?? null,
            'template_data' => $data['template_data'] ?? null,
            'budget' => $data['budget'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function updateCampaign(string $uuid, array $data): CrmCampaign
    {
        $campaign = CrmCampaign::where('uuid', $uuid)->firstOrFail();
        $campaign->update($data);
        return $campaign->fresh();
    }

    public function updateStatus(string $uuid, string $status): CrmCampaign
    {
        $campaign = CrmCampaign::where('uuid', $uuid)->firstOrFail();
        $campaign->update(['status' => $status]);
        
        if ($status === CrmCampaign::STATUS_RUNNING) {
            $campaign->update(['scheduled_at' => now()]);
        }
        
        if ($status === CrmCampaign::STATUS_COMPLETED) {
            $campaign->calculateMetrics();
        }
        
        return $campaign->fresh();
    }

    public function updateMetrics(string $uuid): CrmCampaign
    {
        $campaign = CrmCampaign::where('uuid', $uuid)->firstOrFail();
        $campaign->calculateMetrics();
        return $campaign->fresh();
    }

    public function getCampaignStats(): array
    {
        return [
            'total' => CrmCampaign::count(),
            'draft' => CrmCampaign::where('status', CrmCampaign::STATUS_DRAFT)->count(),
            'scheduled' => CrmCampaign::where('status', CrmCampaign::STATUS_SCHEDULED)->count(),
            'running' => CrmCampaign::where('status', CrmCampaign::STATUS_RUNNING)->count(),
            'completed' => CrmCampaign::where('status', CrmCampaign::STATUS_COMPLETED)->count(),
            'total_recipients' => CrmCampaign::sum('total_recipients'),
            'total_sent' => CrmCampaign::sum('sent_count'),
            'total_delivered' => CrmCampaign::sum('delivered_count'),
            'total_opened' => CrmCampaign::sum('opened_count'),
            'total_converted' => CrmCampaign::sum('converted_count'),
            'delivery_rate' => CrmCampaign::sum('sent_count') > 0
                ? (CrmCampaign::sum('delivered_count') / CrmCampaign::sum('sent_count')) * 100
                : 0,
            'open_rate' => CrmCampaign::sum('delivered_count') > 0
                ? (CrmCampaign::sum('opened_count') / CrmCampaign::sum('delivered_count')) * 100
                : 0,
        ];
    }
}
