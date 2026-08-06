<?php

declare(strict_types=1);

namespace App\Services\Exam;

use App\Models\Exam\Exam;
use App\Models\Exam\ExamSession;
use App\Models\Exam\ExamCenter;
use App\Models\Exam\Question;
use App\Models\Exam\ExamQuestion;
use App\Models\Exam\ExamResult;
use App\Models\Exam\SeatPlan;
use App\Models\Exam\SeatAssignment;
use App\Models\Exam\AdmitCard;
use App\Models\Exam\CbtSession;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExamService
{
    // ===================== EXAM SESSIONS =====================

    public function getSessions(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = ExamSession::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createSession(array $data): ExamSession
    {
        return DB::transaction(function () use ($data) {
            return ExamSession::create([
                'uuid' => (string) Str::uuid(),
                'name' => $data['name'],
                'name_bn' => $data['name_bn'] ?? null,
                'session' => $data['session'],
                'academic_year' => $data['academic_year'],
                'term' => $data['term'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'status' => $data['status'] ?? 'upcoming',
                'description' => $data['description'] ?? null,
            ]);
        });
    }

    // ===================== EXAM CENTERS =====================

    public function getCenters(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = ExamCenter::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('center_code', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createCenter(array $data): ExamCenter
    {
        return DB::transaction(function () use ($data) {
            return ExamCenter::create([
                'uuid' => (string) Str::uuid(),
                'center_code' => $data['center_code'] ?? $this->generateCenterCode(),
                'name' => $data['name'],
                'name_bn' => $data['name_bn'] ?? null,
                'building' => $data['building'] ?? null,
                'floor' => $data['floor'] ?? null,
                'address' => $data['address'] ?? null,
                'capacity' => $data['capacity'] ?? 40,
                'status' => $data['status'] ?? 'active',
                'description' => $data['description'] ?? null,
            ]);
        });
    }

    private function generateCenterCode(): string
    {
        $prefix = 'C';
        $last = ExamCenter::orderBy('id', 'desc')->first();
        $sequence = $last ? ((int) substr($last->center_code, 1)) + 1 : 1;
        return sprintf('%s%03d', $prefix, $sequence);
    }

    // ===================== EXAMS =====================

    public function getExams(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Exam::with(['session', 'subject', 'teacher', 'center']);

        if (!empty($filters['session_id'])) {
            $query->where('session_id', $filters['session_id']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['exam_type'])) {
            $query->where('exam_type', $filters['exam_type']);
        }

        if (!empty($filters['mode'])) {
            $query->where('mode', $filters['mode']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('exam_code', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('exam_date', 'desc')->paginate($perPage);
    }

    public function createExam(array $data): Exam
    {
        return DB::transaction(function () use ($data) {
            $exam = Exam::create([
                'uuid' => (string) Str::uuid(),
                'exam_code' => Exam::generateExamCode(),
                'title' => $data['title'],
                'title_bn' => $data['title_bn'] ?? null,
                'session_id' => $data['session_id'] ?? null,
                'subject_id' => $data['subject_id'] ?? null,
                'teacher_id' => $data['teacher_id'] ?? null,
                'exam_type' => $data['exam_type'] ?? 'class_test',
                'exam_date' => $data['exam_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'duration' => $data['duration'] ?? 60,
                'full_marks' => $data['full_marks'] ?? 100,
                'pass_marks' => $data['pass_marks'] ?? 33,
                'practical_marks' => $data['practical_marks'] ?? 0,
                'theory_marks' => $data['theory_marks'] ?? 100,
                'center_id' => $data['center_id'] ?? null,
                'mode' => $data['mode'] ?? 'offline',
                'status' => $data['status'] ?? 'draft',
                'description' => $data['description'] ?? null,
                'negative_marking' => $data['negative_marking'] ?? false,
                'negative_mark_value' => $data['negative_mark_value'] ?? 0.25,
            ]);

            return $exam;
        });
    }

    public function publishExam(string $uuid): Exam
    {
        $exam = Exam::where('uuid', $uuid)->firstOrFail();
        $exam->update(['status' => Exam::STATUS_PUBLISHED]);
        return $exam->fresh();
    }

    // ===================== QUESTION BANK =====================

    public function getQuestions(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Question::with(['subject', 'category']);

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['question_type'])) {
            $query->where('question_type', $filters['question_type']);
        }

        if (!empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('question', 'like', "%{$filters['search']}%")
                    ->orWhere('question_code', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createQuestion(array $data): Question
    {
        return DB::transaction(function () use ($data) {
            return Question::create([
                'uuid' => (string) Str::uuid(),
                'question_code' => Question::generateQuestionCode(),
                'subject_id' => $data['subject_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'chapter' => $data['chapter'] ?? null,
                'topic' => $data['topic'] ?? null,
                'question_type' => $data['question_type'] ?? 'mcq',
                'difficulty' => $data['difficulty'] ?? 'medium',
                'marks' => $data['marks'] ?? 1,
                'question' => $data['question'],
                'question_bn' => $data['question_bn'] ?? null,
                'options' => $data['options'] ?? null,
                'correct_answer' => $data['correct_answer'],
                'explanation' => $data['explanation'] ?? null,
                'attachments' => $data['attachments'] ?? null,
                'created_by' => auth()->id(),
                'status' => $data['status'] ?? 'active',
            ]);
        });
    }

    // ===================== SEAT PLAN =====================

    public function generateSeatPlan(array $data): SeatPlan
    {
        return DB::transaction(function () use ($data) {
            $seatPlan = SeatPlan::create([
                'uuid' => (string) Str::uuid(),
                'plan_code' => SeatPlan::generatePlanCode(),
                'exam_id' => $data['exam_id'],
                'center_id' => $data['center_id'],
                'room' => $data['room'] ?? null,
                'floor' => $data['floor'] ?? null,
                'exam_date' => $data['exam_date'],
                'start_time' => $data['start_time'],
                'total_seats' => $data['total_seats'],
                'seats' => $this->generateSeatsLayout($data['rows'], $data['columns']),
                'status' => 'draft',
            ]);

            // Assign students to seats
            if (!empty($data['student_ids'])) {
                $studentIds = $data['student_ids'];
                $seats = $this->generateSeatNumbers($data['rows'], $data['columns']);

                foreach ($studentIds as $index => $studentId) {
                    if ($index >= count($seats)) break;
                    SeatAssignment::create([
                        'uuid' => (string) Str::uuid(),
                        'seat_plan_id' => $seatPlan->id,
                        'student_id' => $studentId,
                        'seat_number' => $seats[$index],
                        'row_number' => substr($seats[$index], 0, 1),
                        'column_number' => substr($seats[$index], 1),
                    ]);
                }
            }

            return $seatPlan->load('assignments');
        });
    }

    private function generateSeatsLayout(int $rows, int $columns): array
    {
        $layout = [];
        for ($r = 1; $r <= $rows; $r++) {
            for ($c = 1; $c <= $columns; $c++) {
                $layout[] = [
                    'row' => chr(64 + $r),
                    'column' => $c,
                    'seat' => chr(64 + $r) . $c,
                ];
            }
        }
        return $layout;
    }

    private function generateSeatNumbers(int $rows, int $columns): array
    {
        $seats = [];
        for ($r = 1; $r <= $rows; $r++) {
            for ($c = 1; $c <= $columns; $c++) {
                $seats[] = chr(64 + $r) . $c;
            }
        }
        return $seats;
    }

    // ===================== ADMIT CARD =====================

    public function generateAdmitCard(array $data): AdmitCard
    {
        return DB::transaction(function () use ($data) {
            return AdmitCard::create([
                'uuid' => (string) Str::uuid(),
                'admit_card_no' => AdmitCard::generateAdmitCardNo(),
                'student_id' => $data['student_id'],
                'student_name' => $data['student_name'],
                'roll_number' => $data['roll_number'],
                'registration_no' => $data['registration_no'] ?? null,
                'session_id' => $data['session_id'],
                'exam_id' => $data['exam_id'] ?? null,
                'center_id' => $data['center_id'],
                'photo' => $data['photo'] ?? null,
                'qr_code' => Str::random(32),
                'issue_date' => now(),
                'exam_date' => $data['exam_date'],
                'status' => 'issued',
            ]);
        });
    }

    // ===================== CBT SESSION =====================

    public function startCbtSession(int $examId, int $studentId): CbtSession
    {
        return DB::transaction(function () use ($examId, $studentId) {
            $exam = Exam::findOrFail($examId);

            return CbtSession::create([
                'uuid' => (string) Str::uuid(),
                'session_token' => CbtSession::generateSessionToken(),
                'exam_id' => $examId,
                'student_id' => $studentId,
                'total_time' => $exam->duration * 60,
                'remaining_time' => $exam->duration * 60,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        });
    }

    // ===================== RESULTS =====================

    public function getResults(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = ExamResult::with(['exam', 'student']);

        if (!empty($filters['exam_id'])) {
            $query->where('exam_id', $filters['exam_id']);
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function autoEvaluate(Exam $exam): void
    {
        $answers = $exam->answers()->whereNotNull('answer')->get();

        foreach ($answers as $answer) {
            $question = $answer->question;
            $isCorrect = strtolower(trim($answer->answer)) === strtolower(trim($question->correct_answer));

            $obtainedMarks = $isCorrect ? (float) $question->marks : 0;
            $negativeMarks = 0;

            if (!$isCorrect && $exam->negative_marking) {
                $negativeMarks = (float) $exam->negative_mark_value;
            }

            $answer->update([
                'is_correct' => $isCorrect,
                'correct_answer' => $question->correct_answer,
                'obtained_marks' => $obtainedMarks,
                'negative_marks' => $negativeMarks,
                'status' => $isCorrect ? 'answered' : 'not_answered',
            ]);
        }
    }

    // ===================== DASHBOARD =====================

    public function getDashboardStats(): array
    {
        return [
            'total_exams' => Exam::count(),
            'upcoming_exams' => Exam::where('exam_date', '>=', now()->toDateString())
                ->where('status', 'published')
                ->count(),
            'today_exams' => Exam::whereDate('exam_date', now()->toDateString())
                ->where('status', 'published')
                ->count(),
            'completed_exams' => Exam::where('status', 'completed')->count(),
            'pending_evaluation' => ExamResult::where('status', 'pending')->count(),
            'published_results' => ExamResult::where('status', 'published')->count(),
            'total_questions' => Question::count(),
            'total_centers' => ExamCenter::count(),
            'total_sessions' => ExamSession::count(),
        ];
    }
}
