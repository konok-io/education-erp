<?php

declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmAnnouncement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_announcements';

    const TYPE_GENERAL = 'general';
    const TYPE_ACADEMIC = 'academic';
    const TYPE_EXAM = 'exam';
    const TYPE_HOLIDAY = 'holiday';
    const TYPE_EVENT = 'event';
    const TYPE_EMERGENCY = 'emergency';
    const TYPE_ADMINISTRATIVE = 'administrative';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'uuid',
        'announcement_no',
        'title',
        'content',
        'announcement_type',
        'priority',
        'status',
        'created_by',
        'publish_date',
        'end_date',
        'is_pinned',
        'show_on_website',
        'show_on_portal',
        'send_notification',
        'target_audience',
        'attachments',
        'view_count',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'end_date' => 'date',
        'is_pinned' => 'boolean',
        'show_on_website' => 'boolean',
        'show_on_portal' => 'boolean',
        'send_notification' => 'boolean',
        'target_audience' => 'array',
        'attachments' => 'array',
        'view_count' => 'integer',
    ];

    public static function generateAnnouncementNo(): string
    {
        $prefix = 'ANN';
        $year = date('Y');
        $lastAnnouncement = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastAnnouncement ? ((int) substr($lastAnnouncement->announcement_no, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public static function announcementTypes(): array
    {
        return [
            self::TYPE_GENERAL => 'General Notice',
            self::TYPE_ACADEMIC => 'Academic Notice',
            self::TYPE_EXAM => 'Exam Notice',
            self::TYPE_HOLIDAY => 'Holiday Notice',
            self::TYPE_EVENT => 'Event Notice',
            self::TYPE_EMERGENCY => 'Emergency Notice',
            self::TYPE_ADMINISTRATIVE => 'Administrative Notice',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}
