<?php

declare(strict_types=1);

namespace App\Models\Alumni;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumniEvent extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'alumni_events';

    protected $fillable = [
        'uuid',
        'event_number',
        'event_title',
        'description',
        'event_type',
        'banner_image',
        'event_date',
        'start_time',
        'end_time',
        'venue',
        'city',
        'country',
        'address',
        'agenda',
        'speakers',
        'max_participants',
        'registered_count',
        'registration_fee',
        'is_free',
        'is_virtual',
        'meeting_link',
        'is_featured',
        'is_active',
        'status',
        'organized_by',
        'published_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'registration_fee' => 'decimal:2',
        'is_free' => 'boolean',
        'is_virtual' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'max_participants' => 'integer',
        'registered_count' => 'integer',
        'published_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== TYPES =====================
    public const TYPE_REUNION = 'reunion';
    public const TYPE_SEMINAR = 'seminar';
    public const TYPE_WORKSHOP = 'workshop';
    public const TYPE_CONFERENCE = 'conference';
    public const TYPE_NETWORKING = 'networking';
    public const TYPE_WEBINAR = 'webinar';

    // ===================== RELATIONSHIPS =====================

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'organized_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class, 'event_id');
    }

    // ===================== SCOPES =====================

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString());
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ===================== METHODS =====================

    public static function generateEventNumber(): string
    {
        $prefix = 'EVT';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function eventTypes(): array
    {
        return [
            self::TYPE_REUNION => 'Reunion',
            self::TYPE_SEMINAR => 'Seminar',
            self::TYPE_WORKSHOP => 'Workshop',
            self::TYPE_CONFERENCE => 'Conference',
            self::TYPE_NETWORKING => 'Networking Event',
            self::TYPE_WEBINAR => 'Webinar',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_ONGOING => 'Ongoing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function publish(): void
    {
        $this->update([
            'status' => self::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
    }

    public function hasCapacity(): bool
    {
        if (!$this->max_participants) {
            return true;
        }
        return $this->registered_count < $this->max_participants;
    }
}
