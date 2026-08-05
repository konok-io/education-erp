<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Models\CRM\CrmTicket;
use App\Models\CRM\CrmTicketReply;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketService
{
    public function getTickets(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = CrmTicket::with(['contact', 'creator', 'assignee']);

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (!empty($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('ticket_no', 'like', "%{$search}%");
            });
        }

        return $query->orderByRaw("FIELD(priority, 'critical', 'urgent', 'high', 'medium', 'low')")
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function createTicket(array $data): CrmTicket
    {
        return CrmTicket::create([
            'uuid' => (string) Str::uuid(),
            'ticket_no' => CrmTicket::generateTicketNo(),
            'subject' => $data['subject'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'],
            'priority' => $data['priority'] ?? CrmTicket::PRIORITY_MEDIUM,
            'status' => CrmTicket::STATUS_OPEN,
            'contact_id' => $data['contact_id'] ?? null,
            'created_by' => $data['created_by'],
            'assigned_to' => $data['assigned_to'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'cc' => $data['cc'] ?? null,
            'attachments' => $data['attachments'] ?? null,
            'tags' => $data['tags'] ?? null,
            'related_student_id' => $data['related_student_id'] ?? null,
            'related_employee_id' => $data['related_employee_id'] ?? null,
        ]);
    }

    public function assignTicket(string $uuid, int $assigneeId): CrmTicket
    {
        $ticket = CrmTicket::where('uuid', $uuid)->firstOrFail();
        $ticket->update([
            'assigned_to' => $assigneeId,
            'status' => CrmTicket::STATUS_ASSIGNED,
        ]);
        return $ticket->fresh();
    }

    public function updateStatus(string $uuid, string $status): CrmTicket
    {
        $ticket = CrmTicket::where('uuid', $uuid)->firstOrFail();
        
        $updateData = ['status' => $status];
        
        if ($status === CrmTicket::STATUS_IN_PROGRESS && !$ticket->first_response_at) {
            $updateData['first_response_at'] = now();
        }
        
        if ($status === CrmTicket::STATUS_RESOLVED) {
            $updateData['resolved_at'] = now();
        }
        
        if ($status === CrmTicket::STATUS_CLOSED) {
            $updateData['closed_at'] = now();
            $updateData['resolved_at'] = $ticket->resolved_at ?? now();
        }
        
        $ticket->update($updateData);
        $ticket->calculateResolutionTime();
        
        return $ticket->fresh();
    }

    public function addReply(string $ticketUuid, array $data): CrmTicketReply
    {
        $ticket = CrmTicket::where('uuid', $ticketUuid)->firstOrFail();
        
        $reply = CrmTicketReply::create([
            'uuid' => (string) Str::uuid(),
            'ticket_id' => $ticket->id,
            'user_id' => $data['user_id'],
            'message' => $data['message'],
            'attachments' => $data['attachments'] ?? null,
            'is_internal' => $data['is_internal'] ?? false,
            'is_customer_reply' => $data['is_customer_reply'] ?? false,
        ]);
        
        // Update ticket response count and status
        if (!$data['is_internal'] ?? false) {
            $ticket->increment('response_count');
            
            if ($ticket->status === CrmTicket::STATUS_OPEN || $ticket->status === CrmTicket::STATUS_ASSIGNED) {
                $ticket->update([
                    'status' => CrmTicket::STATUS_IN_PROGRESS,
                    'first_response_at' => now(),
                ]);
            }
        }
        
        return $reply;
    }

    public function getTicketStats(): array
    {
        return [
            'total' => CrmTicket::count(),
            'open' => CrmTicket::where('status', CrmTicket::STATUS_OPEN)->count(),
            'in_progress' => CrmTicket::where('status', CrmTicket::STATUS_IN_PROGRESS)->count(),
            'waiting' => CrmTicket::where('status', CrmTicket::STATUS_WAITING)->count(),
            'resolved' => CrmTicket::where('status', CrmTicket::STATUS_RESOLVED)->count(),
            'closed' => CrmTicket::where('status', CrmTicket::STATUS_CLOSED)->count(),
            'by_priority' => CrmTicket::selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority'),
            'by_category' => CrmTicket::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category'),
            'avg_resolution_hours' => CrmTicket::whereNotNull('resolution_time_hours')
                ->avg('resolution_time_hours'),
        ];
    }
}
