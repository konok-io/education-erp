<?php

declare(strict_types=1);

namespace App\Services\Result;

use App\Models\Result\Exam;
use App\Models\Result\ExamSchedule;
use App\Models\Result\ExamHall;
use App\Models\Result\Result;
use App\Models\Result\ResultDetail;
use App\Models\Result\GradeRule;
use App\Models\Result\GradeRange;
use App\Models\Result\ReScrutiny;
use App\Models\Student\Student;
use App\Models\Academic\Subject;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResultService
{
    // ===================== EXAM =====================

    public function getExams(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Exam::with(['session', 'academicLevel', 'program', 'semester']);

        if (!empty($filters['session_id'])) {
            $session = \App\Models\Academic\AcademicSession::where('uuid', $filters['session_id'])->first();
            if ($session) {
                $query->where('academic_session_id', $session->id);
            }
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['exam_type'])) {
            $query->where('exam_type', $filters['exam_type']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createExam(array $data): Exam
    {
        return DB::transaction(function () use ($data) {
            return Exam::create([
                'uuid' => (string) Str::uuid(),
                'exam_code' => Exam::generateExamCode(),
                'exam_name' => $data['exam_name'],
                'exam_name_bn' => $data['exam_name_bn'] ?? null,
                'academic_session_id' => $data['session_id'],
                'academic_level_id' => $data['academic_level_id'] ?? null,
                'program_id' => $data['program_id'] ?? null,
                'semester_id' => $data['semester_id'] ?? null,
                'exam_type' => $data['exam_type'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'] ?? null,
                'total_marks' => $data['total_marks'] ?? 100,
                'pass_marks' => $data['pass_marks'] ?? 33,
                'description' => $data['description'] ?? null,
                'instructions' => $data['instructions'] ?? null,
                'status' => Exam::STATUS_DRAFT,
            ]);
        });
    }

    public function updateExam(string $uuid, array $data): Exam
    {
        $exam = Exam::where('uuid', $uuid)->firstOrFail();

        $exam->update(array_intersect_key($data, array_flip([
            'exam_name', 'exam_name_bn', 'start_date', 'end_date',
            'total_marks', 'pass_marks', 'description', 'instructions', 'status'
        ])));

        return $exam->fresh();
    }

    public function deleteExam(string $uuid): bool
    {
        $exam = Exam::where('uuid', $uuid)->firstOrFail();
        return $exam->delete();
    }

    // ===================== MARK ENTRY =====================

    public function entryMarks(int $examId, int $subjectId, array $marksData, int $userId): array
    {
        $results = ['total' => 0, 'success' => 0, 'failed' => 0, 'errors' => []];

        $exam = Exam::findOrFail($examId);
        $subject = Subject::findOrFail($subjectId);

        foreach ($marksData as $item) {
            $results['total']++;

            try {
                $student = Student::where('uuid', $item['student_id'])->first();

                if (!$student) {
                    $results['failed']++;
                    $results['errors'][] = "Student not found: {$item['student_id']}";
                    continue;
                }

                $obtainedMarks = ($item['theory'] ?? 0)
                    + ($item['practical'] ?? 0)
                    + ($item['viva'] ?? 0)
                    + ($item['attendance'] ?? 0)
                    + ($item['assignment'] ?? 0)
                    + ($item['internal'] ?? 0);

                // Get or create result
                $result = Result::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'exam_id' => $examId,
                        'class_id' => $student->class_id,
                        'section_id' => $student->section_id,
                        'session_id' => $exam->academic_session_id,
                        'semester_id' => $exam->semester_id,
                    ],
                    [
                        'uuid' => (string) Str::uuid(),
                        'result_no' => Result::generateResultNo(),
                        'status' => Result::STATUS_DRAFT,
                        'created_by' => $userId,
                    ]
                );

                // Get or create result detail
                $detail = ResultDetail::updateOrCreate(
                    [
                        'result_id' => $result->id,
                        'subject_id' => $subjectId,
                    ],
                    [
                        'uuid' => (string) Str::uuid(),
                        'teacher_id' => $item['teacher_id'] ?? null,
                        'theory_marks' => $item['theory'] ?? 0,
                        'practical_marks' => $item['practical'] ?? 0,
                        'viva_marks' => $item['viva'] ?? 0,
                        'attendance_marks' => $item['attendance'] ?? 0,
                        'assignment_marks' => $item['assignment'] ?? 0,
                        'internal_marks' => $item['internal'] ?? 0,
                        'total_marks' => $subject->full_marks ?? 100,
                        'obtained_marks' => $obtainedMarks,
                        'pass_marks' => $subject->pass_marks ?? 33,
                        'credit' => $subject->credit_hour ?? 3,
                        'entered_by' => $userId,
                        'entered_at' => now(),
                    ]
                );

                // Calculate grade
                $gradeInfo = $detail->calculateGrade();
                $detail->update([
                    'grade_point' => $gradeInfo['point'],
                    'grade' => $gradeInfo['grade'],
                    'is_pass' => $gradeInfo['point'] > 0,
                ]);

                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = $e->getMessage();
            }
        }

        return $results;
    }

    public function updateMarks(string $uuid, array $data): ResultDetail
    {
        $detail = ResultDetail::where('uuid', $uuid)->firstOrFail();

        $obtainedMarks = ($data['theory'] ?? $detail->theory_marks)
            + ($data['practical'] ?? $detail->practical_marks)
            + ($data['viva'] ?? $detail->viva_marks)
            + ($data['attendance'] ?? $detail->attendance_marks)
            + ($data['assignment'] ?? $detail->assignment_marks)
            + ($data['internal'] ?? $detail->internal_marks);

        $detail->update(array_merge($data, [
            'obtained_marks' => $obtainedMarks,
        ]));

        // Recalculate grade
        $gradeInfo = $detail->calculateGrade();
        $detail->update([
            'grade_point' => $gradeInfo['point'],
            'grade' => $gradeInfo['grade'],
            'is_pass' => $gradeInfo['point'] > 0,
        ]);

        return $detail->fresh();
    }

    // ===================== RESULT PROCESSING =====================

    public function getStudentResults(string $studentId, array $filters = []): array
    {
        $student = Student::where('uuid', $studentId)->firstOrFail();

        $query = Result::where('student_id', $student->id)
            ->with(['exam', 'details.subject']);

        if (!empty($filters['session_id'])) {
            $session = \App\Models\Academic\AcademicSession::where('uuid', $filters['session_id'])->first();
            if ($session) {
                $query->where('session_id', $session->id);
            }
        }

        if (!empty($filters['semester_id'])) {
            $semester = \App\Models\Academic\Semester::where('uuid', $filters['semester_id'])->first();
            if ($semester) {
                $query->where('semester_id', $semester->id);
            }
        }

        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    public function processResults(int $examId): array
    {
        $results = Result::where('exam_id', $examId)->with('details')->get();

        foreach ($results as $result) {
            $totalObtained = 0;
            $totalFull = 0;
            $isPass = true;

            foreach ($result->details as $detail) {
                $totalObtained += $detail->obtained_marks;
                $totalFull += $detail->total_marks;
                if (!$detail->is_pass) {
                    $isPass = false;
                }
            }

            $gpa = $result->calculateGPA();
            $grade = $this->gpaToGrade($gpa);

            $result->update([
                'total_marks' => $totalFull,
                'obtained_marks' => $totalObtained,
                'gpa' => $gpa,
                'grade' => $grade,
                'status' => Result::STATUS_PENDING,
            ]);
        }

        return [
            'processed' => $results->count(),
            'exam_id' => $examId,
        ];
    }

    public function getClassResults(int $examId, array $filters = []): LengthAwarePaginator
    {
        $query = Result::where('exam_id', $examId)
            ->with(['student.profile', 'details.subject']);

        if (!empty($filters['class_id'])) {
            $class = \App\Models\Academic\AcademicClass::where('uuid', $filters['class_id'])->first();
            if ($class) {
                $query->where('class_id', $class->id);
            }
        }

        if (!empty($filters['section_id'])) {
            $section = \App\Models\Academic\Section::where('uuid', $filters['section_id'])->first();
            if ($section) {
                $query->where('section_id', $section->id);
            }
        }

        return $query->orderByDesc('gpa')->paginate(50);
    }

    // ===================== GPA/CGPA =====================

    public function calculateGPA(string $studentId, ?string $semesterId = null): array
    {
        $student = Student::where('uuid', $studentId)->firstOrFail();

        $query = ResultDetail::whereHas('result', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->with('subject');

        if ($semesterId) {
            $semester = \App\Models\Academic\Semester::where('uuid', $semesterId)->first();
            if ($semester) {
                $query->whereHas('result', function ($q) use ($semester) {
                    $q->where('semester_id', $semester->id);
                });
            }
        }

        $details = $query->get();

        $totalPoints = 0;
        $totalCredits = 0;
        $subjects = [];

        foreach ($details as $detail) {
            if ($detail->grade_point > 0) {
                $totalPoints += $detail->grade_point * $detail->credit;
                $totalCredits += $detail->credit;
                $subjects[] = [
                    'subject' => $detail->subject?->subject_name,
                    'grade' => $detail->grade,
                    'point' => $detail->grade_point,
                    'credit' => $detail->credit,
                ];
            }
        }

        $gpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;

        return [
            'gpa' => $gpa,
            'total_credits' => $totalCredits,
            'subjects' => $subjects,
        ];
    }

    public function calculateCGPA(string $studentId): array
    {
        $student = Student::where('uuid', $studentId)->firstOrFail();

        $details = ResultDetail::whereHas('result', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->get();

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($details as $detail) {
            if ($detail->grade_point > 0) {
                $totalPoints += $detail->grade_point * $detail->credit;
                $totalCredits += $detail->credit;
            }
        }

        $cgpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;

        return [
            'cgpa' => $cgpa,
            'total_credits' => $totalCredits,
            'total_subjects' => $details->count(),
        ];
    }

    // ===================== PUBLISH/APPROVE =====================

    public function verifyResult(string $uuid, int $userId): void
    {
        $result = Result::where('uuid', $uuid)->firstOrFail();
        $result->update([
            'status' => Result::STATUS_VERIFIED,
            'verified_by' => $userId,
            'verified_at' => now(),
        ]);
    }

    public function approveResult(string $uuid, int $userId): void
    {
        $result = Result::where('uuid', $uuid)->firstOrFail();
        $result->update([
            'status' => Result::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function publishResults(int $examId, int $userId): void
    {
        $exam = Exam::findOrFail($examId);
        $exam->publish($userId);

        Result::where('exam_id', $examId)
            ->whereIn('status', [Result::STATUS_APPROVED, Result::STATUS_PENDING])
            ->update([
                'is_published' => true,
                'status' => Result::STATUS_PUBLISHED,
            ]);
    }

    public function lockResult(string $uuid): void
    {
        $result = Result::where('uuid', $uuid)->firstOrFail();
        $result->update(['status' => Result::STATUS_ARCHIVED]);
    }

    // ===================== TRANSCRIPT/MARKSHEET =====================

    public function generateTranscript(string $studentId): array
    {
        $student = Student::where('uuid', $studentId)
            ->with('profile', 'class', 'section')
            ->firstOrFail();

        $results = Result::where('student_id', $student->id)
            ->with('exam', 'details.subject')
            ->orderBy('created_at')
            ->get();

        $allDetails = [];
        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($results as $result) {
            foreach ($result->details as $detail) {
                if ($detail->grade_point > 0) {
                    $totalPoints += $detail->grade_point * $detail->credit;
                    $totalCredits += $detail->credit;
                    $allDetails[] = [
                        'semester' => $result->semester?->title,
                        'subject' => $detail->subject?->subject_name,
                        'credit' => $detail->credit,
                        'grade' => $detail->grade,
                        'point' => $detail->grade_point,
                    ];
                }
            }
        }

        $cgpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;

        return [
            'student' => [
                'id' => $student->uuid,
                'name' => $student->profile?->full_name,
                'student_no' => $student->student_no,
                'class' => $student->class?->name,
                'section' => $student->section?->name,
            ],
            'cgpa' => $cgpa,
            'total_credits' => $totalCredits,
            'results' => $allDetails,
        ];
    }

    public function generateMarksheet(string $studentId, int $examId): array
    {
        $student = Student::where('uuid', $studentId)
            ->with('profile', 'class', 'section')
            ->firstOrFail();

        $result = Result::where('student_id', $student->id)
            ->where('exam_id', $examId)
            ->with('exam', 'details.subject')
            ->firstOrFail();

        return [
            'student' => [
                'id' => $student->uuid,
                'name' => $student->profile?->full_name,
                'student_no' => $student->student_no,
                'class' => $student->class?->name,
                'section' => $student->section?->name,
            ],
            'exam' => [
                'name' => $result->exam?->exam_name,
                'date' => $result->exam?->start_date?->toDateString(),
            ],
            'total_marks' => $result->total_marks,
            'obtained_marks' => $result->obtained_marks,
            'gpa' => $result->gpa,
            'grade' => $result->grade,
            'status' => $result->status,
            'details' => $result->details->map(fn($d) => [
                'subject' => $d->subject?->subject_name,
                'total' => $d->total_marks,
                'obtained' => $d->obtained_marks,
                'grade' => $d->grade,
                'point' => $d->grade_point,
            ])->toArray(),
        ];
    }

    // ===================== MERIT LIST =====================

    public function generateMeritList(int $examId, ?string $classId, ?string $sectionId, int $limit = 100): array
    {
        $query = Result::where('exam_id', $examId)
            ->with('student.profile')
            ->orderByDesc('gpa');

        if ($classId) {
            $class = \App\Models\Academic\AcademicClass::where('uuid', $classId)->first();
            if ($class) {
                $query->where('class_id', $class->id);
            }
        }

        if ($sectionId) {
            $section = \App\Models\Academic\Section::where('uuid', $sectionId)->first();
            if ($section) {
                $query->where('section_id', $section->id);
            }
        }

        $results = $query->limit($limit)->get();

        $meritList = [];
        $position = 1;

        foreach ($results as $result) {
            $meritList[] = [
                'position' => $position++,
                'student_id' => $result->student?->uuid,
                'student_no' => $result->student?->student_no,
                'name' => $result->student?->profile?->full_name,
                'obtained_marks' => $result->obtained_marks,
                'gpa' => $result->gpa,
                'grade' => $result->grade,
            ];
        }

        return $meritList;
    }

    public function getFailList(int $examId, ?string $classId): array
    {
        $query = ResultDetail::whereHas('result', function ($q) use ($examId) {
            $q->where('exam_id', $examId)->where('is_pass', false);
        })->with(['result.student.profile', 'subject']);

        if ($classId) {
            $class = \App\Models\Academic\AcademicClass::where('uuid', $classId)->first();
            if ($class) {
                $query->whereHas('result', function ($q) use ($class) {
                    $q->where('class_id', $class->id);
                });
            }
        }

        return $query->get()->map(fn($d) => [
            'student' => $d->result?->student?->profile?->full_name,
            'student_no' => $d->result?->student?->student_no,
            'subject' => $d->subject?->subject_name,
            'obtained_marks' => $d->obtained_marks,
            'pass_marks' => $d->pass_marks,
        ])->toArray();
    }

    // ===================== ANALYTICS =====================

    public function getAnalytics(int $examId): array
    {
        $results = Result::where('exam_id', $examId)->with('details')->get();

        $totalStudents = $results->count();
        $passedStudents = $results->filter(fn($r) => $r->gpa >= 1.0)->count();
        $failedStudents = $results->filter(fn($r) => $r->gpa < 1.0)->count();

        $gradeDistribution = $results->groupBy('grade')->map->count();

        $avgGPA = $totalStudents > 0 ? round($results->avg('gpa'), 2) : 0;
        $highestGPA = $totalStudents > 0 ? round($results->max('gpa'), 2) : 0;
        $lowestGPA = $totalStudents > 0 ? round($results->min('gpa'), 2) : 0;

        return [
            'total_students' => $totalStudents,
            'passed' => $passedStudents,
            'failed' => $failedStudents,
            'pass_rate' => $totalStudents > 0 ? round(($passedStudents / $totalStudents) * 100, 2) : 0,
            'average_gpa' => $avgGPA,
            'highest_gpa' => $highestGPA,
            'lowest_gpa' => $lowestGPA,
            'grade_distribution' => $gradeDistribution,
        ];
    }

    public function getSubjectAnalysis(int $examId, ?int $subjectId = null): array
    {
        $query = ResultDetail::whereHas('result', function ($q) use ($examId) {
            $q->where('exam_id', $examId);
        })->with('subject');

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        $details = $query->get();

        $totalStudents = $details->count();
        $passed = $details->filter(fn($d) => $d->is_pass)->count();

        return [
            'total_students' => $totalStudents,
            'passed' => $passed,
            'failed' => $totalStudents - $passed,
            'pass_rate' => $totalStudents > 0 ? round(($passed / $totalStudents) * 100, 2) : 0,
            'average_marks' => $totalStudents > 0 ? round($details->avg('obtained_marks'), 2) : 0,
            'highest_marks' => $totalStudents > 0 ? round($details->max('obtained_marks'), 2) : 0,
            'lowest_marks' => $totalStudents > 0 ? round($details->min('obtained_marks'), 2) : 0,
        ];
    }

    // ===================== RE-SCRUTINY =====================

    public function applyReScrutiny(int $resultDetailId, string $reason, float $feeAmount = 0): ReScrutiny
    {
        return ReScrutiny::create([
            'uuid' => (string) Str::uuid(),
            'application_no' => ReScrutiny::generateApplicationNo(),
            'result_detail_id' => $resultDetailId,
            'reason' => $reason,
            'fee_amount' => $feeAmount,
            'status' => $feeAmount > 0 ? ReScrutiny::STATUS_PENDING : ReScrutiny::STATUS_PAID,
        ]);
    }

    public function reviewReScrutiny(
        string $uuid,
        string $status,
        ?float $newMarks,
        ?string $changeReason,
        ?string $notes,
        int $userId
    ): ReScrutiny {
        $scrutiny = ReScrutiny::where('uuid', $uuid)->firstOrFail();

        $updateData = [
            'status' => $status === 'completed' ? ReScrutiny::STATUS_COMPLETED : ReScrutiny::STATUS_REJECTED,
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
            'new_marks' => $newMarks,
            'change_reason' => $changeReason,
            'notes' => $notes,
        ];

        if ($status === 'completed' && $newMarks !== null) {
            $scrutiny->resultDetail->update(['obtained_marks' => $newMarks]);
        }

        $scrutiny->update($updateData);

        return $scrutiny->fresh();
    }

    // ===================== GRADE RULES =====================

    public function getGradeRules(): array
    {
        return GradeRule::with('ranges')->get()->toArray();
    }

    public function createGradeRule(array $data): GradeRule
    {
        return DB::transaction(function () use ($data) {
            $rule = GradeRule::create([
                'uuid' => (string) Str::uuid(),
                'name' => $data['name'],
                'name_bn' => $data['name_bn'] ?? null,
                'scale_type' => $data['scale_type'] ?? GradeRule::SCALE_FIVE,
                'is_active' => true,
                'is_default' => $data['is_default'] ?? false,
            ]);

            if (!empty($data['ranges'])) {
                foreach ($data['ranges'] as $range) {
                    GradeRange::create([
                        'uuid' => (string) Str::uuid(),
                        'grade_rule_id' => $rule->id,
                        'grade' => $range['grade'],
                        'grade_bn' => $range['grade_bn'] ?? null,
                        'min_percentage' => $range['min'],
                        'max_percentage' => $range['max'],
                        'grade_point' => $range['point'],
                    ]);
                }
            }

            return $rule->fresh('ranges');
        });
    }

    // ===================== EXPORT =====================

    public function exportResults(int $examId, string $format, array $filters = []): string
    {
        $filename = "results_exam_{$examId}_" . now()->format('Ymd_His');
        return url("storage/exports/{$filename}.{$format}");
    }

    // ===================== HELPERS =====================

    private function gpaToGrade(float $gpa): string
    {
        if ($gpa >= 5.00) return 'A+';
        if ($gpa >= 4.00) return 'A';
        if ($gpa >= 3.50) return 'A-';
        if ($gpa >= 3.00) return 'B';
        if ($gpa >= 2.00) return 'C';
        if ($gpa >= 1.00) return 'D';
        return 'F';
    }
}
