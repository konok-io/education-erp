<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Models\CRM\CrmLead;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadService
{
    public function getLeads(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = CrmLead::with(['contact', 'assignedCounselor']);

        if (!empty($filters['pipeline_stage'])) {
            $query->where('pipeline_stage', $filters['pipeline_stage']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['lead_source'])) {
            $query->where('lead_source', $filters['lead_source']);
        }

        if (!empty($filters['assigned_counselor_id'])) {
            $query->where('assigned_counselor_id', $filters['assigned_counselor_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('lead_no', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createLead(array $data): CrmLead
    {
        return CrmLead::create([
            'uuid' => (string) Str::uuid(),
            'lead_no' => CrmLead::generateLeadNo(),
            'contact_id' => $data['contact_id'] ?? null,
            'full_name' => $data['full_name'],
            'mobile' => $data['mobile'] ?? null,
            'email' => $data['email'] ?? null,
            'lead_source' => $data['lead_source'],
            'course_interested' => $data['course_interested'] ?? null,
            'session' => $data['session'] ?? null,
            'assigned_counselor_id' => $data['assigned_counselor_id'] ?? null,
            'priority' => $data['priority'] ?? CrmLead::PRIORITY_MEDIUM,
            'pipeline_stage' => CrmLead::STAGE_NEW,
            'lead_score' => 0,
            'expected_admission_date' => $data['expected_admission_date'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => CrmLead::STATUS_ACTIVE,
        ]);
    }

    public function updateLeadStage(string $uuid, string $stage): CrmLead
    {
        $lead = CrmLead::where('uuid', $uuid)->firstOrFail();
        $lead->update(['pipeline_stage' => $stage]);
        $lead->updateLeadScore();
        return $lead->fresh();
    }

    public function assignCounselor(string $uuid, int $counselorId): CrmLead
    {
        $lead = CrmLead::where('uuid', $uuid)->firstOrFail();
        $lead->update(['assigned_counselor_id' => $counselorId]);
        return $lead->fresh();
    }

    public function convertToStudent(string $uuid, int $studentId): CrmLead
    {
        return DB::transaction(function () use ($uuid, $studentId) {
            $lead = CrmLead::where('uuid', $uuid)->firstOrFail();
            
            $lead->update([
                'status' => CrmLead::STATUS_CONVERTED,
                'pipeline_stage' => CrmLead::STAGE_ADMISSION,
                'converted_to_student_id' => $studentId,
                'converted_at' => now(),
            ]);

            return $lead->fresh();
        });
    }

    public function getLeadPipelineStats(): array
    {
        $stages = CrmLead::pipelineStages();
        $stats = [];

        foreach ($stages as $key => $label) {
            $stats[$key] = [
                'label' => $label,
                'count' => CrmLead::where('pipeline_stage', $key)
                    ->where('status', CrmLead::STATUS_ACTIVE)
                    ->count(),
            ];
        }

        return $stats;
    }

    public function getLeadStats(): array
    {
        return [
            'total' => CrmLead::count(),
            'active' => CrmLead::where('status', CrmLead::STATUS_ACTIVE)->count(),
            'converted' => CrmLead::where('status', CrmLead::STATUS_CONVERTED)->count(),
            'lost' => CrmLead::where('status', CrmLead::STATUS_LOST)->count(),
            'today_new' => CrmLead::whereDate('created_at', now()->toDateString())
                ->where('pipeline_stage', CrmLead::STAGE_NEW)
                ->count(),
            'followup_due' => CrmLead::where('next_followup', '<=', now()->toDateString())
                ->where('status', CrmLead::STATUS_ACTIVE)
                ->count(),
            'by_source' => CrmLead::selectRaw('lead_source, COUNT(*) as count')
                ->groupBy('lead_source')
                ->pluck('count', 'lead_source'),
            'pipeline' => $this->getLeadPipelineStats(),
        ];
    }
}
