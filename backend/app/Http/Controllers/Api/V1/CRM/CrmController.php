<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\CRM;

use App\Http\Controllers\Controller;
use App\Services\CRM\ContactService;
use App\Services\CRM\LeadService;
use App\Services\CRM\TicketService;
use App\Services\CRM\CampaignService;
use App\Models\CRM\CrmContact;
use App\Models\CRM\CrmLead;
use App\Models\CRM\CrmTicket;
use App\Models\CRM\CrmCampaign;
use App\Models\CRM\CrmInquiry;
use App\Models\CRM\CrmFollowup;
use App\Models\CRM\CrmFeedback;
use App\Models\CRM\CrmAnnouncement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    public function __construct(
        private readonly ContactService $contactService,
        private readonly LeadService $leadService,
        private readonly TicketService $ticketService,
        private readonly CampaignService $campaignService
    ) {}

    // ===================== DASHBOARD =====================

    public function getDashboard(): JsonResponse
    {
        $stats = [
            'contacts' => $this->contactService->getContactStats(),
            'leads' => $this->leadService->getLeadStats(),
            'tickets' => $this->ticketService->getTicketStats(),
            'campaigns' => $this->campaignService->getCampaignStats(),
            'today_inquiries' => CrmInquiry::whereDate('created_at', now()->toDateString())->count(),
            'open_tickets' => CrmTicket::whereIn('status', [
                CrmTicket::STATUS_OPEN,
                CrmTicket::STATUS_ASSIGNED,
                CrmTicket::STATUS_IN_PROGRESS,
            ])->count(),
            'closed_tickets' => CrmTicket::where('status', CrmTicket::STATUS_CLOSED)->count(),
            'pending_followups' => CrmFollowup::where('status', CrmFollowup::STATUS_PENDING)
                ->whereDate('scheduled_date', '<=', now()->toDateString())
                ->count(),
        ];

        return response()->json(['data' => $stats]);
    }

    // ===================== CONTACTS =====================

    public function getContacts(Request $request): JsonResponse
    {
        $filters = $request->only(['contact_type', 'status', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $contacts = $this->contactService->getContacts($perPage, $filters);

        return response()->json([
            'data' => $contacts->items(),
            'meta' => [
                'current_page' => $contacts->currentPage(),
                'last_page' => $contacts->lastPage(),
                'per_page' => $contacts->perPage(),
                'total' => $contacts->total(),
            ],
        ]);
    }

    public function createContact(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'photo' => 'nullable|string',
            'contact_type' => 'required|in:' . implode(',', array_keys(CrmContact::contactTypes())),
            'mobile' => 'nullable|string|max:20',
            'alternative_mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'district' => 'nullable|string|max:100',
            'division' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'organization' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:100',
            'student_id' => 'nullable|exists:students,id',
            'guardian_id' => 'nullable|exists:guardians,id',
            'employee_id' => 'nullable|exists:employees,id',
            'social_links' => 'nullable|array',
            'tags' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $contact = $this->contactService->createContact($validated);
        return response()->json(['data' => $contact], 201);
    }

    public function updateContact(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'photo' => 'nullable|string',
            'contact_type' => 'sometimes|in:' . implode(',', array_keys(CrmContact::contactTypes())),
            'mobile' => 'nullable|string|max:20',
            'alternative_mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|in:active,inactive,blocked',
        ]);

        $contact = $this->contactService->updateContact($uuid, $validated);
        return response()->json(['data' => $contact]);
    }

    // ===================== LEADS =====================

    public function getLeads(Request $request): JsonResponse
    {
        $filters = $request->only([
            'pipeline_stage', 'status', 'priority', 'lead_source',
            'assigned_counselor_id', 'search', 'date_from', 'date_to'
        ]);
        $perPage = (int) $request->get('per_page', 20);

        $leads = $this->leadService->getLeads($perPage, $filters);

        return response()->json([
            'data' => $leads->items(),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
            ],
        ]);
    }

    public function createLead(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'contact_id' => 'nullable|exists:crm_contacts,id',
            'full_name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'lead_source' => 'required|in:' . implode(',', array_keys(CrmLead::leadSources())),
            'course_interested' => 'nullable|string|max:255',
            'session' => 'nullable|string|max:50',
            'assigned_counselor_id' => 'nullable|exists:users,id',
            'priority' => 'nullable|in:' . implode(',', array_keys(CrmLead::priorities())),
            'expected_admission_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $lead = $this->leadService->createLead($validated);
        return response()->json(['data' => $lead], 201);
    }

    public function updateLeadStage(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'stage' => 'required|in:' . implode(',', array_keys(CrmLead::pipelineStages())),
        ]);

        $lead = $this->leadService->updateLeadStage($uuid, $validated['stage']);
        return response()->json(['data' => $lead]);
    }

    public function assignCounselor(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'counselor_id' => 'required|exists:users,id',
        ]);

        $lead = $this->leadService->assignCounselor($uuid, $validated['counselor_id']);
        return response()->json(['data' => $lead]);
    }

    public function getLeadPipeline(): JsonResponse
    {
        $pipeline = $this->leadService->getLeadPipelineStats();
        return response()->json(['data' => $pipeline]);
    }

    // ===================== TICKETS =====================

    public function getTickets(Request $request): JsonResponse
    {
        $filters = $request->only(['category', 'status', 'priority', 'assigned_to', 'created_by', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $tickets = $this->ticketService->getTickets($perPage, $filters);

        return response()->json([
            'data' => $tickets->items(),
            'meta' => [
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
            ],
        ]);
    }

    public function createTicket(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:' . implode(',', array_keys(CrmTicket::categories())),
            'priority' => 'nullable|in:' . implode(',', array_keys(CrmTicket::priorities())),
            'contact_id' => 'nullable|exists:crm_contacts,id',
            'assigned_to' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'cc' => 'nullable|array',
            'attachments' => 'nullable|array',
            'tags' => 'nullable|array',
            'related_student_id' => 'nullable|exists:students,id',
            'related_employee_id' => 'nullable|exists:employees,id',
        ]);

        $validated['created_by'] = auth()->id();
        $ticket = $this->ticketService->createTicket($validated);
        return response()->json(['data' => $ticket], 201);
    }

    public function assignTicket(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'assignee_id' => 'required|exists:users,id',
        ]);

        $ticket = $this->ticketService->assignTicket($uuid, $validated['assignee_id']);
        return response()->json(['data' => $ticket]);
    }

    public function updateTicketStatus(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(CrmTicket::statuses())),
        ]);

        $ticket = $this->ticketService->updateStatus($uuid, $validated['status']);
        return response()->json(['data' => $ticket]);
    }

    public function addTicketReply(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'attachments' => 'nullable|array',
            'is_internal' => 'nullable|boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $reply = $this->ticketService->addReply($uuid, $validated);
        return response()->json(['data' => $reply], 201);
    }

    public function getTicketStats(): JsonResponse
    {
        $stats = $this->ticketService->getTicketStats();
        return response()->json(['data' => $stats]);
    }

    // ===================== CAMPAIGNS =====================

    public function getCampaigns(Request $request): JsonResponse
    {
        $filters = $request->only(['campaign_type', 'channel', 'status', 'date_from', 'date_to']);
        $perPage = (int) $request->get('per_page', 20);

        $campaigns = $this->campaignService->getCampaigns($perPage, $filters);

        return response()->json([
            'data' => $campaigns->items(),
            'meta' => [
                'current_page' => $campaigns->currentPage(),
                'last_page' => $campaigns->lastPage(),
                'per_page' => $campaigns->perPage(),
                'total' => $campaigns->total(),
            ],
        ]);
    }

    public function createCampaign(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'campaign_type' => 'required|in:' . implode(',', array_keys(CrmCampaign::campaignTypes())),
            'channel' => 'required|in:' . implode(',', array_keys(CrmCampaign::channels())),
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'scheduled_at' => 'nullable|date',
            'target_audience' => 'nullable|string',
            'audience_filters' => 'nullable|array',
            'template_data' => 'nullable|array',
            'budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $campaign = $this->campaignService->createCampaign($validated);
        return response()->json(['data' => $campaign], 201);
    }

    public function updateCampaignStatus(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,scheduled,running,paused,completed,cancelled',
        ]);

        $campaign = $this->campaignService->updateStatus($uuid, $validated['status']);
        return response()->json(['data' => $campaign]);
    }

    public function getCampaignStats(): JsonResponse
    {
        $stats = $this->campaignService->getCampaignStats();
        return response()->json(['data' => $stats]);
    }
}
