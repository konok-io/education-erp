<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Models\HR\TrainingType;
use App\Models\HR\TrainingRecord;
use App\Models\HR\ServiceBook;
use App\Models\Employee\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TrainingService
{
    // ===================== TRAINING TYPES =====================

    public function getTrainingTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return TrainingType::where('is_active', true)->orderBy('name')->get();
    }

    public function createTrainingType(array $data): TrainingType
    {
        return TrainingType::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'name_bn' => $data['name_bn'] ?? null,
            'code' => $data['code'],
            'description' => $data['description'] ?? null,
            'is_active' => true,
        ]);
    }

    public function initializeDefaultTypes(): void
    {
        $defaults = TrainingType::defaultTypes();
        foreach ($defaults as $type) {
            TrainingType::firstOrCreate(
                ['code' => $type['code']],
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $type['name'],
                    'code' => $type['code'],
                    'is_monetary' => $type['is_monetary'] ?? false,
                    'is_active' => true,
                ]
            );
        }
    }

    // ===================== TRAINING RECORDS =====================

    public function getTrainingRecords(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = TrainingRecord::with(['employee.profile', 'trainingType']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['training_type_id'])) {
            $query->where('training_type_id', $filters['training_type_id']);
        }

        if (!empty($filters['result'])) {
            $query->where('result', $filters['result']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('start_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('end_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createTrainingRecord(array $data): TrainingRecord
    {
        return DB::transaction(function () use ($data) {
            $record = TrainingRecord::create([
                'uuid' => (string) Str::uuid(),
                'training_no' => TrainingRecord::generateTrainingNo(),
                'employee_id' => $data['employee_id'],
                'training_type_id' => $data['training_type_id'],
                'training_name' => $data['training_name'],
                'organizer' => $data['organizer'] ?? null,
                'venue' => $data['venue'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'] ?? null,
                'duration_days' => $data['duration_days'] ?? 1,
                'duration_hours' => $data['duration_hours'] ?? 0,
                'certificate_number' => $data['certificate_number'] ?? null,
                'certificate_date' => $data['certificate_date'] ?? null,
                'result' => TrainingRecord::RESULT_PENDING,
                'cost' => $data['cost'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Create service book entry
            ServiceBook::create([
                'uuid' => (string) Str::uuid(),
                'employee_id' => $data['employee_id'],
                'entry_no' => ServiceBook::generateEntryNo(),
                'entry_date' => $data['start_date'],
                'event_type' => ServiceBook::EVENT_TRAINING,
                'title' => $data['training_name'],
                'description' => "Training Type: {$record->trainingType->name}",
                'metadata' => [
                    'training_id' => $record->id,
                    'organizer' => $data['organizer'] ?? null,
                    'duration' => $record->duration,
                ],
            ]);

            return $record;
        });
    }

    public function updateTrainingResult(string $uuid, array $data): TrainingRecord
    {
        $record = TrainingRecord::where('uuid', $uuid)->firstOrFail();

        $record->update([
            'result' => $data['result'],
            'feedback' => $data['feedback'] ?? null,
            'score' => $data['score'] ?? null,
            'certificate_number' => $data['certificate_number'] ?? null,
            'certificate_date' => $data['certificate_date'] ?? null,
            'certificate_file' => $data['certificate_file'] ?? null,
        ]);

        return $record->fresh();
    }

    public function getEmployeeTrainingHistory(int $employeeId): \Illuminate\Database\Eloquent\Collection
    {
        return TrainingRecord::where('employee_id', $employeeId)
            ->with('trainingType')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function getTrainingStats(): array
    {
        return [
            'total_trainings' => TrainingRecord::count(),
            'completed' => TrainingRecord::whereIn('result', [
                TrainingRecord::RESULT_PASSED,
                TrainingRecord::RESULT_EXCELLENT,
                TrainingRecord::RESULT_VERY_GOOD,
                TrainingRecord::RESULT_GOOD,
            ])->count(),
            'ongoing' => TrainingRecord::where('result', TrainingRecord::RESULT_PENDING)->count(),
            'total_cost' => TrainingRecord::sum('cost'),
            'by_type' => TrainingRecord::selectRaw('training_type_id, COUNT(*) as count')
                ->groupBy('training_type_id')
                ->with('trainingType')
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => $item->trainingType->name ?? 'Unknown',
                        'count' => $item->count,
                    ];
                }),
        ];
    }
}
