<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Models\HR\OnboardingChecklist;
use App\Models\HR\EmployeeOnboarding;
use App\Models\HR\OnboardingCompletion;
use App\Models\HR\OfferLetter;
use App\Models\HR\ServiceBook;
use App\Models\Employee\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OnboardingService
{
    // ===================== ONBOARDING CHECKLISTS =====================

    public function getChecklists(?string $category = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = OnboardingChecklist::where('is_active', true);
        if ($category) {
            $query->where('category', $category);
        }
        return $query->orderBy('order')->get();
    }

    public function createChecklist(array $data): OnboardingChecklist
    {
        return OnboardingChecklist::create([
            'uuid' => (string) Str::uuid(),
            'checklist_name' => $data['checklist_name'],
            'category' => $data['category'],
            'order' => $data['order'] ?? 0,
            'description' => $data['description'] ?? null,
            'is_required' => $data['is_required'] ?? true,
            'is_active' => true,
        ]);
    }

    public function initializeDefaultChecklists(): void
    {
        $defaults = OnboardingChecklist::defaultChecklists();
        foreach ($defaults as $checklist) {
            OnboardingChecklist::firstOrCreate(
                ['checklist_name' => $checklist['name']],
                [
                    'uuid' => (string) Str::uuid(),
                    'category' => $checklist['category'],
                    'order' => $checklist['order'],
                    'is_required' => true,
                    'is_active' => true,
                ]
            );
        }
    }

    // ===================== EMPLOYEE ONBOARDING =====================

    public function getOnboardings(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = EmployeeOnboarding::with(['employee.profile', 'assignedUser']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function startOnboarding(array $data): EmployeeOnboarding
    {
        return DB::transaction(function () use ($data) {
            // Create onboarding record
            $onboarding = EmployeeOnboarding::create([
                'uuid' => (string) Str::uuid(),
                'onboarding_no' => EmployeeOnboarding::generateOnboardingNo(),
                'employee_id' => $data['employee_id'],
                'offer_letter_id' => $data['offer_letter_id'] ?? null,
                'start_date' => $data['start_date'],
                'status' => EmployeeOnboarding::STATUS_IN_PROGRESS,
                'assigned_to' => $data['assigned_to'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Create completion records for all active checklists
            $checklists = OnboardingChecklist::where('is_active', true)->get();
            foreach ($checklists as $checklist) {
                OnboardingCompletion::create([
                    'uuid' => (string) Str::uuid(),
                    'employee_onboarding_id' => $onboarding->id,
                    'checklist_id' => $checklist->id,
                    'is_completed' => false,
                ]);
            }

            return $onboarding;
        });
    }

    public function completeChecklist(string $onboardingUuid, int $checklistId, int $userId, ?string $remarks = null): OnboardingCompletion
    {
        $onboarding = EmployeeOnboarding::where('uuid', $onboardingUuid)->firstOrFail();

        $completion = OnboardingCompletion::where('employee_onboarding_id', $onboarding->id)
            ->where('checklist_id', $checklistId)
            ->firstOrFail();

        $completion->markComplete($userId, $remarks);

        // Check if all required checklists are completed
        $this->checkOnboardingCompletion($onboarding);

        return $completion->fresh();
    }

    public function checkOnboardingCompletion(EmployeeOnboarding $onboarding): void
    {
        $requiredChecklists = OnboardingCompletion::where('employee_onboarding_id', $onboarding->id)
            ->whereHas('checklist', function ($q) {
                $q->where('is_required', true);
            })
            ->where('is_completed', false)
            ->count();

        if ($requiredChecklists === 0) {
            $onboarding->update([
                'status' => EmployeeOnboarding::STATUS_COMPLETED,
                'completion_date' => now(),
            ]);
        }
    }

    public function completeOnboarding(string $uuid, int $userId): EmployeeOnboarding
    {
        $onboarding = EmployeeOnboarding::where('uuid', $uuid)->firstOrFail();

        $onboarding->update([
            'status' => EmployeeOnboarding::STATUS_COMPLETED,
            'completion_date' => now(),
        ]);

        // Create service book entry
        ServiceBook::create([
            'uuid' => (string) Str::uuid(),
            'employee_id' => $onboarding->employee_id,
            'entry_no' => ServiceBook::generateEntryNo(),
            'entry_date' => now(),
            'event_type' => ServiceBook::EVENT_JOINING,
            'title' => 'Onboarding Completed',
            'description' => 'Employee onboarding process completed successfully.',
            'approved_by' => $userId,
            'approved_date' => now(),
        ]);

        return $onboarding->fresh();
    }

    public function getOnboardingProgress(string $uuid): array
    {
        $onboarding = EmployeeOnboarding::where('uuid', $uuid)->firstOrFail();
        $completions = OnboardingCompletion::where('employee_onboarding_id', $onboarding->id)
            ->with('checklist')
            ->get();

        $byCategory = [];
        foreach ($completions as $completion) {
            $category = $completion->checklist->category;
            if (!isset($byCategory[$category])) {
                $byCategory[$category] = ['total' => 0, 'completed' => 0];
            }
            $byCategory[$category]['total']++;
            if ($completion->is_completed) {
                $byCategory[$category]['completed']++;
            }
        }

        return [
            'onboarding' => $onboarding,
            'completions' => $completions,
            'by_category' => $byCategory,
            'percentage' => $onboarding->completion_percentage,
        ];
    }

    // ===================== STATISTICS =====================

    public function getOnboardingStats(): array
    {
        return [
            'pending' => EmployeeOnboarding::where('status', EmployeeOnboarding::STATUS_PENDING)->count(),
            'in_progress' => EmployeeOnboarding::where('status', EmployeeOnboarding::STATUS_IN_PROGRESS)->count(),
            'completed' => EmployeeOnboarding::where('status', EmployeeOnboarding::STATUS_COMPLETED)->count(),
            'total' => EmployeeOnboarding::count(),
        ];
    }
}
