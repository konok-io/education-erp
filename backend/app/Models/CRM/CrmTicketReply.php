<?php

declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmTicketReply extends Model
{
    use HasFactory;

    protected $table = 'crm_ticket_replies';

    protected $fillable = [
        'uuid',
        'ticket_id',
        'user_id',
        'message',
        'attachments',
        'is_internal',
        'is_customer_reply',
        'is_autoresponse',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal' => 'boolean',
        'is_customer_reply' => 'boolean',
        'is_autoresponse' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(CrmTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
