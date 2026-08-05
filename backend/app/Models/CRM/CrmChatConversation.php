<?php

declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmChatConversation extends Model
{
    use HasFactory;

    protected $table = 'crm_chat_conversations';

    const SOURCE_WEBSITE = 'website';
    const SOURCE_STUDENT_PORTAL = 'student_portal';
    const SOURCE_ADMIN_DASHBOARD = 'admin_dashboard';
    const SOURCE_MOBILE_APP = 'mobile_app';

    const STATUS_WAITING = 'waiting';
    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'uuid',
        'conversation_no',
        'contact_id',
        'visitor_name',
        'visitor_email',
        'visitor_ip',
        'source',
        'status',
        'assigned_agent_id',
        'first_response_at',
        'closed_at',
        'closed_by',
        'closing_note',
        'message_count',
        'duration_minutes',
        'rating',
        'feedback',
    ];

    protected $casts = [
        'first_response_at' => 'datetime',
        'closed_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public static function generateConversationNo(): string
    {
        $prefix = 'CHAT';
        $date = date('Ymd');
        $lastChat = self::whereDate('created_at', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastChat ? ((int) substr($lastChat->conversation_no, -4)) + 1 : 1;
        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'contact_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_agent_id');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'closed_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CrmChatMessage::class, 'conversation_id');
    }
}
