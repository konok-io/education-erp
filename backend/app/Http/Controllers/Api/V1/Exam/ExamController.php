<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Exam;

use App\Http\Controllers\Controller;
use App\Services\Exam\ExamService;
use App\Models\Exam\Exam;
use App\Models\Exam\ExamSession;
use App\Models\Exam\ExamCenter;
use App\Models\Exam\Question;
use App\Models\Exam\QuestionCategory;
use App\Models\Exam\ExamResult;
use App\Models\Exam\SeatPlan;
use App\Models\Exam\AdmitCard;
use App\Models\Exam\CbtSession;
use App\Models\Exam\ExamAnswer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct(
        private readonly ExamService $examService
    ) {}

    // ===================== DASHBOARD =====================

    public function getDashboard(): JsonResponse
    {
        $stats = $this->examService->getDashboardStats();
        return response()->json(['data' => $stats]);
    }

    // ===================== EXAM SESSIONS =====================

    public function getSessions(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'academic_year']);
        $perPage = (int) $request->get('per_page', 20);

        $sessions = $this->examService->getSessions($perPage, $filters);

        return response()->json([
            'data' => $sessions->items(),
            'meta' => [
                'current_page' => $sessions->currentPage(),
                'last_page' => $sessions->lastPage(),
                'total' => $sessions->total(),
            ],
        ]);
    }

    public function createSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'session' => 'required|string|max:50',
            'academic_year' => 'required|integer',
            'term' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $session = $this->examService->createSession($validated);
        return response()->json(['data' => $session], 201);
    }

    // ===================== EXAM CENTERS =====================

    public function getCenters(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $centers = $this->examService->getCenters($perPage, $filters);

        return response()->json([
            'data' => $centers->items(),
            'meta' => [
                'current_page' => $centers->currentPage(),
                'last_page' => $centers->lastPage(),
                'total' => $centers->total(),
            ],
        ]);
    }

    public function createCenter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'center_code' => 'nullable|string|max:50|unique:exam_centers',
            'building' => 'nullable|string|max:150',
            'floor' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $center = $this->examService->createCenter($validated);
        return response()->json(['data' => $center], 201);
    }

    // ===================== EXAMS =====================

    public function getExams(Request $request): JsonResponse
    {
        $filters = $request->only(['session_id', 'subject_id', 'exam_type', 'mode', 'status', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $exams = $this->examService->getExams($perPage, $filters);

        return response()->json([
            'data' => $exams->items(),
            'meta' => [
                'current_page' => $exams->currentPage(),
                'last_page' => $exams->lastPage(),
                'total' => $exams->total(),
            ],
        ]);
    }

    public function createExam(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'session_id' => 'nullable|exists:exam_sessions,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:users,id',
            'exam_type' => 'nullable|in:class_test,quiz,assignment,mid_term,final,model_test,admission,practical,viva,improvement,supplementary',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'duration' => 'nullable|integer|min:1',
            'full_marks' => 'nullable|numeric|min:0',
            'pass_marks' => 'nullable|numeric|min:0',
            'practical_marks' => 'nullable|numeric|min:0',
            'theory_marks' => 'nullable|numeric|min:0',
            'center_id' => 'nullable|exists:exam_centers,id',
            'mode' => 'nullable|in:online,offline,cbt,omr',
            'negative_marking' => 'nullable|boolean',
            'negative_mark_value' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $exam = $this->examService->createExam($validated);
        return response()->json(['data' => $exam], 201);
    }

    public function showExam(string $uuid): JsonResponse
    {
        $exam = Exam::with(['session', 'subject', 'teacher', 'center', 'questions.question'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $exam]);
    }

    public function publishExam(string $uuid): JsonResponse
    {
        $exam = $this->examService->publishExam($uuid);
        return response()->json(['data' => $exam]);
    }

    // ===================== QUESTION BANK =====================

    public function getQuestions(Request $request): JsonResponse
    {
        $filters = $request->only(['subject_id', 'category_id', 'question_type', 'difficulty', 'status', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $questions = $this->examService->getQuestions($perPage, $filters);

        return response()->json([
            'data' => $questions->items(),
            'meta' => [
                'current_page' => $questions->currentPage(),
                'last_page' => $questions->lastPage(),
                'total' => $questions->total(),
            ],
        ]);
    }

    public function createQuestion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject_id' => 'nullable|exists:subjects,id',
            'category_id' => 'nullable|exists:question_categories,id',
            'chapter' => 'nullable|string|max:100',
            'topic' => 'nullable|string|max:100',
            'question_type' => 'nullable|in:mcq,cq,written,short,true_false,fill_blank,matching,programming,math,diagram',
            'difficulty' => 'nullable|in:easy,medium,hard,expert',
            'marks' => 'nullable|numeric|min:0.5',
            'question' => 'required|string',
            'question_bn' => 'nullable|string',
            'options' => 'nullable|array',
            'correct_answer' => 'required|string',
            'explanation' => 'nullable|string',
            'attachments' => 'nullable|array',
        ]);

        $question = $this->examService->createQuestion($validated);
        return response()->json(['data' => $question], 201);
    }

    public function showQuestion(string $uuid): JsonResponse
    {
        $question = Question::with(['subject', 'category', 'creator'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $question]);
    }

    // ===================== QUESTION CATEGORIES =====================

    public function getQuestionCategories(): JsonResponse
    {
        $categories = QuestionCategory::whereNull('parent_id')
            ->with('children')
            ->get();

        return response()->json(['data' => $categories]);
    }

    // ===================== SEAT PLANS =====================

    public function getSeatPlans(Request $request): JsonResponse
    {
        $query = SeatPlan::with(['exam', 'center', 'assignments']);

        if ($request->exam_id) {
            $query->where('exam_id', $request->exam_id);
        }

        $plans = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $plans->items(),
            'meta' => [
                'current_page' => $plans->currentPage(),
                'last_page' => $plans->lastPage(),
                'total' => $plans->total(),
            ],
        ]);
    }

    public function generateSeatPlan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'center_id' => 'required|exists:exam_centers,id',
            'room' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:50',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'total_seats' => 'required|integer|min:1',
            'rows' => 'required|integer|min:1|max:10',
            'columns' => 'required|integer|min:1|max:10',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $seatPlan = $this->examService->generateSeatPlan($validated);
        return response()->json(['data' => $seatPlan], 201);
    }

    // ===================== ADMIT CARDS =====================

    public function getAdmitCards(Request $request): JsonResponse
    {
        $query = AdmitCard::with(['student', 'session', 'exam', 'center']);

        if ($request->session_id) {
            $query->where('session_id', $request->session_id);
        }

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        $cards = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $cards->items(),
            'meta' => [
                'current_page' => $cards->currentPage(),
                'last_page' => $cards->lastPage(),
                'total' => $cards->total(),
            ],
        ]);
    }

    public function generateAdmitCard(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'student_name' => 'required|string|max:255',
            'roll_number' => 'required|string|max:50',
            'registration_no' => 'nullable|string|max:50',
            'session_id' => 'required|exists:exam_sessions,id',
            'exam_id' => 'nullable|exists:exams,id',
            'center_id' => 'required|exists:exam_centers,id',
            'photo' => 'nullable|string',
            'exam_date' => 'required|date',
        ]);

        $admitCard = $this->examService->generateAdmitCard($validated);
        return response()->json(['data' => $admitCard], 201);
    }

    // ===================== RESULTS =====================

    public function getResults(Request $request): JsonResponse
    {
        $filters = $request->only(['exam_id', 'student_id', 'status']);
        $perPage = (int) $request->get('per_page', 20);

        $results = $this->examService->getResults($perPage, $filters);

        return response()->json([
            'data' => $results->items(),
            'meta' => [
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'total' => $results->total(),
            ],
        ]);
    }

    public function evaluateExam(string $uuid): JsonResponse
    {
        $exam = Exam::where('uuid', $uuid)->firstOrFail();

        // Auto evaluate MCQ answers
        $this->examService->autoEvaluate($exam);

        return response()->json([
            'message' => 'Evaluation completed',
            'data' => $exam->load('results'),
        ]);
    }

    // ===================== CBT =====================

    public function startCbtSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'student_id' => 'required|exists:students,id',
        ]);

        $session = $this->examService->startCbtSession(
            $validated['exam_id'],
            $validated['student_id']
        );

        return response()->json(['data' => $session], 201);
    }

    public function submitAnswer(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'nullable|string',
            'status' => 'nullable|in:answered,not_answered,marked_review',
        ]);

        $session = CbtSession::where('uuid', $uuid)->firstOrFail();

        ExamAnswer::updateOrCreate(
            [
                'exam_id' => $session->exam_id,
                'student_id' => $session->student_id,
                'question_id' => $validated['question_id'],
            ],
            [
                'answer' => $validated['answer'] ?? null,
                'status' => $validated['status'] ?? 'answered',
            ]
        );

        return response()->json(['message' => 'Answer saved']);
    }
}
