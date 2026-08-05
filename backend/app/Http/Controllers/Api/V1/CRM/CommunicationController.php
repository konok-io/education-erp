<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\CRM;

use App\Http\Controllers\Controller;
use App\Models\CRM\CrmCommunication;
use App\Models\CRM\CrmAnnouncement;
use App\Models\CRM\CrmFeedback;
use App\Models\CRM\CrmSurvey;
use App\Models\CRM\CrmSurveyResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommunicationController extends Controller
{
    // ===================== COMMUNICATIONS =====================

    public function getCommunications(Request $request): JsonResponse
    {
        $filters = $request->only(['channel', 'direction', 'delivery_status', 'contact_id']);
        $perPage = (int) $request->get('per_page', 20);

        $query = CrmCommunication::with(['contact', 'sender']);

        if (!empty($filters['channel'])) {
            $query->where('channel', $filters['channel']);
        }

        if (!empty($filters['direction'])) {
            $query->where('direction', $filters['direction']);
        }

        if (!empty($filters['delivery_status'])) {
            $query->where('delivery_status', $filters['delivery_status']);
        }

        if (!empty($filters['contact_id'])) {
            $query->where('contact_id', $filters['contact_id']);
        }

        $communications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $communications->items(),
            'meta' => [
                'current_page' => $communications->currentPage(),
                'last_page' => $communications->lastPage(),
                'per_page' => $communications->perPage(),
                'total' => $communications->total(),
            ],
        ]);
    }

    public function sendCommunication(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'contact_id' => 'nullable|exists:crm_contacts,id',
            'channel' => 'required|in:email,sms,whatsapp,push,phone',
            'direction' => 'nullable|in:inbound,outbound',
            'type' => 'nullable|in:transactional,promotional,notification,reminder,campaign,autoresponse,broadcast',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'recipient_email' => 'nullable|email',
            'recipient_mobile' => 'nullable|string|max:20',
            'metadata' => 'nullable|array',
            'attachments' => 'nullable|array',
        ]);

        $communication = CrmCommunication::create([
            'uuid' => (string) Str::uuid(),
            'communication_no' => CrmCommunication::generateCommunicationNo(),
            'contact_id' => $validated['contact_id'] ?? null,
            'channel' => $validated['channel'],
            'direction' => $validated['direction'] ?? CrmCommunication::DIRECTION_OUTBOUND,
            'type' => $validated['type'] ?? CrmCommunication::TYPE_TRANSACTIONAL,
            'subject' => $validated['subject'] ?? null,
            'content' => $validated['content'],
            'recipient_email' => $validated['recipient_email'] ?? null,
            'recipient_mobile' => $validated['recipient_mobile'] ?? null,
            'metadata' => $validated['metadata'] ?? null,
            'attachments' => $validated['attachments'] ?? null,
            'delivery_status' => CrmCommunication::STATUS_QUEUED,
            'sent_by' => auth()->id(),
        ]);

        // Here you would dispatch to a queue for actual sending
        // For now, mark as sent
        $communication->markAsSent();

        return response()->json(['data' => $communication], 201);
    }

    // ===================== ANNOUNCEMENTS =====================

    public function getAnnouncements(Request $request): JsonResponse
    {
        $filters = $request->only(['announcement_type', 'status']);
        $perPage = (int) $request->get('per_page', 20);

        $query = CrmAnnouncement::with(['creator']);

        if (!empty($filters['announcement_type'])) {
            $query->where('announcement_type', $filters['announcement_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->where('status', CrmAnnouncement::STATUS_PUBLISHED);
        }

        $announcements = $query->orderBy('is_pinned', 'desc')
            ->orderBy('publish_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => $announcements->items(),
            'meta' => [
                'current_page' => $announcements->currentPage(),
                'last_page' => $announcements->lastPage(),
                'per_page' => $announcements->perPage(),
                'total' => $announcements->total(),
            ],
        ]);
    }

    public function createAnnouncement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'announcement_type' => 'required|in:' . implode(',', array_keys(CrmAnnouncement::announcementTypes())),
            'priority' => 'nullable|in:low,medium,high,urgent',
            'publish_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'is_pinned' => 'nullable|boolean',
            'show_on_website' => 'nullable|boolean',
            'show_on_portal' => 'nullable|boolean',
            'send_notification' => 'nullable|boolean',
            'target_audience' => 'nullable|array',
            'attachments' => 'nullable|array',
        ]);

        $validated['uuid'] = (string) Str::uuid();
        $validated['announcement_no'] = CrmAnnouncement::generateAnnouncementNo();
        $validated['status'] = CrmAnnouncement::STATUS_DRAFT;
        $validated['created_by'] = auth()->id();

        $announcement = CrmAnnouncement::create($validated);
        return response()->json(['data' => $announcement], 201);
    }

    public function publishAnnouncement(string $uuid): JsonResponse
    {
        $announcement = CrmAnnouncement::where('uuid', $uuid)->firstOrFail();
        $announcement->update([
            'status' => CrmAnnouncement::STATUS_PUBLISHED,
            'publish_date' => now(),
        ]);
        return response()->json(['data' => $announcement->fresh()]);
    }

    // ===================== FEEDBACK =====================

    public function getFeedbacks(Request $request): JsonResponse
    {
        $filters = $request->only(['feedback_type', 'status', 'rating']);
        $perPage = (int) $request->get('per_page', 20);

        $query = CrmFeedback::with(['contact', 'student']);

        if (!empty($filters['feedback_type'])) {
            $query->where('feedback_type', $filters['feedback_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $feedbacks->items(),
            'meta' => [
                'current_page' => $feedbacks->currentPage(),
                'last_page' => $feedbacks->lastPage(),
                'per_page' => $feedbacks->perPage(),
                'total' => $feedbacks->total(),
            ],
        ]);
    }

    public function submitFeedback(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'feedback_type' => 'required|in:' . implode(',', array_keys(CrmFeedback::feedbackTypes())),
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'contact_id' => 'nullable|exists:crm_contacts,id',
            'student_id' => 'nullable|exists:students,id',
            'metadata' => 'nullable|array',
            'attachments' => 'nullable|array',
        ]);

        $validated['uuid'] = (string) Str::uuid();
        $validated['feedback_no'] = CrmFeedback::generateFeedbackNo();
        $validated['status'] = CrmFeedback::STATUS_SUBMITTED;
        $validated['ip_address'] = $request->ip();

        $feedback = CrmFeedback::create($validated);
        return response()->json(['data' => $feedback], 201);
    }

    // ===================== SURVEYS =====================

    public function getSurveys(Request $request): JsonResponse
    {
        $filters = $request->only(['survey_type', 'status']);
        $perPage = (int) $request->get('per_page', 20);

        $query = CrmSurvey::with(['creator']);

        if (!empty($filters['survey_type'])) {
            $query->where('survey_type', $filters['survey_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $surveys = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $surveys->items(),
            'meta' => [
                'current_page' => $surveys->currentPage(),
                'last_page' => $surveys->lastPage(),
                'per_page' => $surveys->perPage(),
                'total' => $surveys->total(),
            ],
        ]);
    }

    public function createSurvey(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'survey_type' => 'required|in:' . implode(',', array_keys(CrmSurvey::surveyTypes())),
            'questions' => 'required|array',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_anonymous' => 'nullable|boolean',
            'allow_multiple' => 'nullable|boolean',
            'show_results' => 'nullable|boolean',
            'target_audience' => 'nullable|array',
        ]);

        $validated['uuid'] = (string) Str::uuid();
        $validated['survey_no'] = CrmSurvey::generateSurveyNo();
        $validated['status'] = CrmSurvey::STATUS_DRAFT;
        $validated['created_by'] = auth()->id();

        $survey = CrmSurvey::create($validated);
        return response()->json(['data' => $survey], 201);
    }

    public function submitSurveyResponse(Request $request, string $uuid): JsonResponse
    {
        $survey = CrmSurvey::where('uuid', $uuid)->firstOrFail();
        
        if ($survey->status !== CrmSurvey::STATUS_ACTIVE) {
            return response()->json(['error' => 'Survey is not active'], 400);
        }

        $validated = $request->validate([
            'responses' => 'required|array',
            'comments' => 'nullable|string',
        ]);

        $response = CrmSurveyResponse::create([
            'uuid' => (string) Str::uuid(),
            'survey_id' => $survey->id,
            'respondent_id' => auth()->id(),
            'responses' => $validated['responses'],
            'comments' => $validated['comments'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Update survey response count
        $survey->increment('total_responses');
        $survey->calculateAverageRating();

        return response()->json(['data' => $response], 201);
    }
}
