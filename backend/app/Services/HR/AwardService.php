<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Models\HR\AwardType;
use App\Models\HR\EmployeeAward;
use App\Models\HR\ServiceBook;
use App\Models\Employee\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AwardService
{
    // ===================== AWARD TYPES =====================

    public function getAwardTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return AwardType::where('is_active', true)->orderBy('name')->get();
    }

    public function createAwardType(array $data): AwardType
    {
        return AwardType::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'name_bn' => $data['name_bn'] ?? null,
            'code' => $data['code'],
            'description' => $data['description'] ?? null,
            'default_reward' => $data['default_reward'] ?? null,
            'is_monetary' => $data['is_monetary'] ?? false,
            'is_active' => true,
        ]);
    }

    public function initializeDefaultTypes(): void
    {
        $defaults = AwardType::defaultTypes();
        foreach ($defaults as $type) {
            AwardType::firstOrCreate(
                ['code' => $type['code']],
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $type['name'],
                    'code' => $type['code'],
                    'is_monetary' => $type['is_monetary'],
                    'is_active' => true,
                ]
            );
        }
    }

    // ===================== AWARDS =====================

    public function getAwards(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = EmployeeAward::with(['employee.profile', 'awardType']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['award_type_id'])) {
            $query->where('award_type_id', $filters['award_type_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('award_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('award_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('award_date', 'desc')->paginate($perPage);
    }

    public function createAward(array $data): EmployeeAward
    {
        return DB::transaction(function () use ($data) {
            $award = EmployeeAward::create([
                'uuid' => (string) Str::uuid(),
                'award_no' => EmployeeAward::generateAwardNo(),
                'employee_id' => $data['employee_id'],
                'award_type_id' => $data['award_type_id'],
                'title' => $data['title'] ?? null,
                'award_date' => $data['award_date'],
                'reason' => $data['reason'] ?? null,
                'reward_amount' => $data['reward_amount'] ?? null,
                'reward_type' => $data['reward_type'] ?? EmployeeAward::REWARD_CERTIFICATE,
                'certificate_number' => $data['certificate_number'] ?? null,
                'certificate_date' => $data['certificate_date'] ?? null,
                'certificate_file' => $data['certificate_file'] ?? null,
                'presented_by' => $data['presented_by'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Create service book entry
            ServiceBook::create([
                'uuid' => (string) Str::uuid(),
                'employee_id' => $data['employee_id'],
                'entry_no' => ServiceBook::generateEntryNo(),
                'entry_date' => $data['award_date'],
                'event_type' => ServiceBook::EVENT_AWARD,
                'title' => $award->awardType->name,
                'description' => $data['reason'] ?? "Awarded for {$award->awardType->name}",
                'metadata' => [
                    'award_id' => $award->id,
                    'reward_type' => $data['reward_type'] ?? null,
                    'reward_amount' => $data['reward_amount'] ?? null,
                ],
            ]);

            return $award;
        });
    }

    public function getEmployeeAwards(int $employeeId): \Illuminate\Database\Eloquent\Collection
    {
        return EmployeeAward::where('employee_id', $employeeId)
            ->with('awardType')
            ->orderBy('award_date', 'desc')
            ->get();
    }

    public function getAwardStats(): array
    {
        return [
            'total_awards' => EmployeeAward::count(),
            'total_value' => EmployeeAward::sum('reward_amount'),
            'by_type' => EmployeeAward::selectRaw('award_type_id, COUNT(*) as count, SUM(reward_amount) as total')
                ->groupBy('award_type_id')
                ->with('awardType')
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => $item->awardType->name ?? 'Unknown',
                        'count' => $item->count,
                        'total' => $item->total ?? 0,
                    ];
                }),
            'recent' => EmployeeAward::with(['employee.profile', 'awardType'])
                ->orderBy('award_date', 'desc')
                ->limit(5)
                ->get(),
        ];
    }
}
