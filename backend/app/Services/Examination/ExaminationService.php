<?php

declare(strict_types=1);

namespace App\Services\Examination;

use App\Models\Examination\Exam;
use App\Models\Examination\ExamActivity;
use App\Models\Examination\ExamAdmitCard;
use App\Models\Examination\ExamAttendance;
use App\Models\Examination\ExamCommittee;
use App\Models\Examination\ExamHall;
use App\Models\Examination\ExamInvigilator;
use App\Models\Examination\ExamMalpractice;
use App\Models\Examination\ExamMark;
use App\Models\Examination\ExamSeatPlan;
use App\Models\Examination\ExamSession;
use App\Models\Examination\ExamSubject;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExaminationService
{
    // ===================== SESSION METHODS =====================

    public function getSessions(array $filters = []): LengthAwarePaginator
    {
        $query = ExamSession::query();

        if (!empty($filters['search'])) {
            $query->where('session_name', 'like', "%{$filters['search']}%");
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->orderByDesc('created_at');
        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function createSession(array $data): ExamSession
    {
        return DB::transaction(function () use ($data) {
            if (!empty($data['is_current'])) {
                ExamSession::where('is_current', true)->update(['is_current' => false]);
            }
            return ExamSession::create($data);
        });
    }

    public function setCurrentSession(ExamSession $session): ExamSession
    {
        return DB::transaction(function () use ($session) {
            ExamSession::where('is_current', true)->update(['is_current' => false]);
            $session->update(['is_current' => true]);
            return $session->fresh();
        });
    }

    // ===================== EXAM METHODS =====================

    public function getExams(array $filters = []): LengthAwarePaginator
    {
        $query = Exam::with(['session']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('exam_name', 'like', "%{$filters['search']}%")
                  ->orWhere('exam_code', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['exam_type'])) {
            $query->where('exam_type', $filters['exam_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['exam_session_id'])) {
            $query->where('exam_session_id', $filters['exam_session_id']);
        }

        $query->orderByDesc('created_at');
        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function createExam(array $data): Exam
    {
        $data['exam_code'] = $data['exam_code'] ?? 'EXM-' . strtoupper(Str::random(6));
        $data['created_by'] = auth()->id();

        return DB::transaction(function () use ($data) {
            $exam = Exam::create($data);

            ExamActivity::log(
                ExamActivity::ACTIVITY_EXAM_CREATED,
                'exams',
                $exam->id,
                null,
                $exam->toArray()
            );

            return $exam;
        });
    }

    public function updateExam(Exam $exam, array $data): Exam
    {
        $oldValues = $exam->toArray();

        return DB::transaction(function () use ($exam, $data, $oldValues) {
            $exam->update($data);

            ExamActivity::log(
                ExamActivity::ACTIVITY_EXAM_UPDATED,
                'exams',
                $exam->id,
                $oldValues,
                $exam->fresh()->toArray()
            );

            return $exam->fresh();
        });
    }

    public function publishExam(Exam $exam): Exam
    {
        return DB::transaction(function () use ($exam) {
            $exam->publish();

            ExamActivity::log(
                ExamActivity::ACTIVITY_EXAM_PUBLISHED,
                'exams',
                $exam->id
            );

            return $exam->fresh();
        });
    }

    // ===================== SUBJECT METHODS =====================

    public function getSubjects(array $filters = []): LengthAwarePaginator
    {
        $query = ExamSubject::with(['exam']);

        if (!empty($filters['exam_id'])) {
            $query->where('exam_id', $filters['exam_id']);
        }

        $query->orderBy('exam_date')->orderBy('start_time');
        return $query->paginate($filters['per_page'] ?? 50);
    }

    public function createSubject(array $data): ExamSubject
    {
        return ExamSubject::create($data);
    }

    public function updateSubject(ExamSubject $subject, array $data): ExamSubject
    {
        $subject->update($data);
        return $subject->fresh();
    }

    // ===================== HALL METHODS =====================

    public function getHalls(array $filters = []): LengthAwarePaginator
    {
        $query = ExamHall::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('hall_name', 'like', "%{$filters['search']}%")
                  ->orWhere('hall_code', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->active();
        $query->orderBy('hall_name');
        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function createHall(array $data): ExamHall
    {
        $data['hall_code'] = $data['hall_code'] ?? 'HALL-' . strtoupper(substr(uniqid(), -6));
        return ExamHall::create($data);
    }

    public function updateHall(ExamHall $hall, array $data): ExamHall
    {
        $hall->update($data);
        return $hall->fresh();
    }

    // ===================== COMMITTEE METHODS =====================

    public function getCommittees(array $filters = []): LengthAwarePaginator
    {
        $query = ExamCommittee::with(['session', 'chairman', 'controller']);

        if (!empty($filters['exam_session_id'])) {
            $query->where('exam_session_id', $filters['exam_session_id']);
        }

        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function createCommittee(array $data): ExamCommittee
    {
        $data['committee_code'] = $data['committee_code'] ?? 'COMM-' . strtoupper(Str::random(6));
        return ExamCommittee::create($data);
    }

    // ===================== INVIGILATOR METHODS =====================

    public function getInvigilators(array $filters = []): LengthAwarePaginator
    {
        $query = ExamInvigilator::with(['exam', 'user', 'hall', 'subject']);

        if (!empty($filters['exam_id'])) {
            $query->where('exam_id', $filters['exam_id']);
        }

        if (!empty($filters['duty_date'])) {
            $query->whereDate('duty_date', $filters['duty_date']);
        }

        return $query->orderBy('duty_date')->paginate($filters['per_page'] ?? 50);
    }

    public function assignInvigilator(array $data): ExamInvigilator
    {
        return DB::transaction(function () use ($data) {
            $invigilator = ExamInvigilator::create($data);

            ExamActivity::log(
                ExamActivity::ACTIVITY_INVIGILATOR_ASSIGNED,
                'exam_invigilators',
                $invigilator->id
            );

            return $invigilator;
        });
    }

    // ===================== SEAT PLAN METHODS =====================

    public function generateSeatPlan(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $examId = $data['exam_id'];
            $subjectId = $data['exam_subject_id'];
            $hallId = $data['exam_hall_id'];
            $students = $data['students'];

            $hall = ExamHall::findOrFail($hallId);
            $seatPlans = [];

            $position = 1;
            for ($row = 1; $row <= $hall->rows; $row++) {
                for ($col = 1; $col <= $hall->columns; $col++) {
                    if ($position > count($students)) {
                        break 2;
                    }

                    $student = $students[$position - 1];
                    $seatNumber = ExamSeatPlan::generateSeatNumber($hall, $row, $col);

                    $seatPlan = ExamSeatPlan::create([
                        'exam_id' => $examId,
                        'exam_subject_id' => $subjectId,
                        'exam_hall_id' => $hallId,
                        'row_number' => $row,
                        'column_number' => $col,
                        'seat_number' => $seatNumber,
                        'student_id' => $student['id'],
                        'student_name' => $student['name'],
                        'student_roll' => $student['roll'],
                        'registration_no' => $student['registration_no'] ?? null,
                    ]);

                    $seatPlans[] = $seatPlan;
                    $position++;
                }
            }

            ExamActivity::log(
                ExamActivity::ACTIVITY_SEAT_PLAN_GENERATED,
                'exam_seat_plans',
                null,
                null,
                ['exam_id' => $examId, 'hall_id' => $hallId, 'count' => count($seatPlans)]
            );

            return $seatPlans;
        });
    }

    public function getSeatPlans(array $filters = []): LengthAwarePaginator
    {
        $query = ExamSeatPlan::with(['exam', 'subject', 'hall']);

        if (!empty($filters['exam_id'])) {
            $query->where('exam_id', $filters['exam_id']);
        }

        if (!empty($filters['exam_subject_id'])) {
            $query->where('exam_subject_id', $filters['exam_subject_id']);
        }

        if (!empty($filters['exam_hall_id'])) {
            $query->where('exam_hall_id', $filters['exam_hall_id']);
        }

        return $query->orderBy('seat_number')->paginate($filters['per_page'] ?? 100);
    }

    // ===================== ADMIT CARD METHODS =====================

    public function generateAdmitCards(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $examId = $data['exam_id'];
            $students = $data['students'];
            $admitCards = [];

            foreach ($students as $student) {
                $admitCard = ExamAdmitCard::create([
                    'admit_card_no' => ExamAdmitCard::generateAdmitCardNo(),
                    'exam_id' => $examId,
                    'student_id' => $student['id'],
                    'student_name' => $student['name'],
                    'student_roll' => $student['roll'],
                    'registration_no' => $student['registration_no'] ?? null,
                    'class_name' => $student['class_name'] ?? null,
                    'section' => $student['section'] ?? null,
                    'verification_token' => ExamAdmitCard::generateVerificationToken(),
                    'issue_date' => now(),
                    'valid_until' => $data['valid_until'] ?? null,
                ]);

                $admitCards[] = $admitCard;
            }

            ExamActivity::log(
                ExamActivity::ACTIVITY_ADMIT_CARD_GENERATED,
                'exam_admit_cards',
                null,
                null,
                ['exam_id' => $examId, 'count' => count($admitCards)]
            );

            return $admitCards;
        });
    }

    public function getAdmitCards(array $filters = []): LengthAwarePaginator
    {
        $query = ExamAdmitCard::with(['exam']);

        if (!empty($filters['exam_id'])) {
            $query->where('exam_id', $filters['exam_id']);
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('created_at')->paginate($filters['per_page'] ?? 20);
    }

    public function verifyAdmitCard(string $token): ?ExamAdmitCard
    {
        $admitCard = ExamAdmitCard::where('verification_token', $token)->first();

        if ($admitCard && (!$admitCard->valid_until || $admitCard->valid_until >= now())) {
            return $admitCard;
        }

        return null;
    }

    // ===================== ATTENDANCE METHODS =====================

    public function recordAttendance(array $data): ExamAttendance
    {
        return DB::transaction(function () use ($data) {
            $data['recorded_by'] = auth()->id();
            $attendance = ExamAttendance::create($data);

            ExamActivity::log(
                ExamActivity::ACTIVITY_ATTENDANCE_SUBMITTED,
                'exam_attendances',
                $attendance->id
            );

            return $attendance;
        });
    }

    public function bulkRecordAttendance(array $records): void
    {
        DB::transaction(function () use ($records) {
            foreach ($records as $record) {
                $record['recorded_by'] = auth()->id();
                ExamAttendance::create($record);
            }
        });
    }

    public function getAttendances(array $filters = []): LengthAwarePaginator
    {
        $query = ExamAttendance::with(['subject', 'hall']);

        if (!empty($filters['exam_subject_id'])) {
            $query->where('exam_subject_id', $filters['exam_subject_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('created_at')->paginate($filters['per_page'] ?? 50);
    }

    // ===================== MARKS METHODS =====================

    public function enterMarks(array $data): ExamMark
    {
        return DB::transaction(function () use ($data) {
            $data['entered_by'] = auth()->id();

            $mark = ExamMark::updateOrCreate(
                [
                    'exam_subject_id' => $data['exam_subject_id'],
                    'student_id' => $data['student_id'],
                ],
                $data
            );

            $mark->calculateTotal();
            
            if ($mark->subject) {
                $mark->evaluateResult((float) $mark->subject->pass_marks);
            }

            $mark->grade = ExamMark::calculateGrade(
                (float) $mark->total_marks,
                (float) ($mark->subject->full_marks ?? 100)
            );

            $mark->save();

            return $mark;
        });
    }

    public function bulkEnterMarks(array $marks): array
    {
        $results = [];
        foreach ($marks as $markData) {
            $results[] = $this->enterMarks($markData);
        }

        ExamActivity::log(
            ExamActivity::ACTIVITY_MARKS_SUBMITTED,
            'exam_marks',
            null,
            null,
            ['count' => count($results)]
        );

        return $results;
    }

    public function approveMarks(ExamMark $mark): ExamMark
    {
        return DB::transaction(function () use ($mark) {
            $mark->approve();

            ExamActivity::log(
                ExamActivity::ACTIVITY_MARKS_APPROVED,
                'exam_marks',
                $mark->id
            );

            return $mark->fresh();
        });
    }

    public function getMarks(array $filters = []): LengthAwarePaginator
    {
        $query = ExamMark::with(['subject']);

        if (!empty($filters['exam_subject_id'])) {
            $query->where('exam_subject_id', $filters['exam_subject_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('student_roll')->paginate($filters['per_page'] ?? 50);
    }

    // ===================== MALPRACTICE METHODS =====================

    public function reportMalpractice(array $data): ExamMalpractice
    {
        $data['invigilator_id'] = auth()->id();
        return ExamMalpractice::create($data);
    }

    public function getMalpractices(array $filters = []): LengthAwarePaginator
    {
        $query = ExamMalpractice::with(['subject', 'hall', 'invigilator']);

        if (!empty($filters['exam_subject_id'])) {
            $query->where('exam_subject_id', $filters['exam_subject_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('created_at')->paginate($filters['per_page'] ?? 20);
    }

    // ===================== DASHBOARD METHODS =====================

    public function getDashboardData(): array
    {
        return [
            'total_exams' => Exam::count(),
            'upcoming_exams' => Exam::upcoming()->count(),
            'ongoing_exams' => Exam::ongoing()->count(),
            'completed_exams' => Exam::where('status', Exam::STATUS_COMPLETED)->count(),
            'total_halls' => ExamHall::active()->count(),
            'total_seats' => ExamHall::active()->sum('capacity'),
            'total_invigilators' => ExamInvigilator::today()->count(),
            'total_students' => ExamAdmitCard::whereHas('exam', function ($q) {
                $q->whereIn('status', [Exam::STATUS_ONGOING, Exam::STATUS_COMPLETED]);
            })->count(),
            'today_exams' => ExamSubject::today()->count(),
            'pending_marks' => ExamMark::where('status', ExamMark::STATUS_DRAFT)->count(),
            'pending_admit_cards' => ExamAdmitCard::where('status', ExamAdmitCard::STATUS_ISSUED)->count(),
        ];
    }
}
