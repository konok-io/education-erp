<?php

declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmInquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_inquiries';

    const SOURCE_WEBSITE = 'website';
    const SOURCE_PHONE = 'phone';
    const SOURCE_WALKIN = 'walkin';
    const SOURCE_REFERRAL = 'referral';
    const SOURCE_CAMPAIGN = 'campaign';
    const SOURCE_SOCIAL_MEDIA = 'social_media';
    const SOURCE_EDUCATION_FAIR = 'education_fair';

    const STATUS_NEW = 'new';
    const STATUS_CONTACTED = 'contacted';
    const STATUS_FOLLOWUP = 'followup';
    const STATUS_CONVERTED = 'converted';
    const STATUS_NOT_INTERESTED = 'not_interested';

    protected $fillable = [
        'uuid',
        'inquiry_no',
        'student_name',
        'father_name',
        'mother_name',
        'mobile',
        'email',
        'program_id',
        'course_id',
        'session',
        'assigned_counselor_id',
        'inquiry_source',
        'status',
        'remarks',
        'notes',
        'next_followup_date',
        'converted_lead_id',
    ];

    protected $casts = [
        'next_followup_date' => 'date',
    ];

    public static function generateInquiryNo(): string
    {
        $prefix = 'INQ';
        $year = date('Y');
        $lastInquiry = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastInquiry ? ((int) substr($lastInquiry->inquiry_no, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public static function sources(): array
    {
        return [
            self::SOURCE_WEBSITE => 'Website',
            self::SOURCE_PHONE => 'Phone',
            self::SOURCE_WALKIN => 'Walk-in',
            self::SOURCE_REFERRAL => 'Referral',
            self::SOURCE_CAMPAIGN => 'Campaign',
            self::SOURCE_SOCIAL_MEDIA => 'Social Media',
            self::SOURCE_EDUCATION_FAIR => 'Education Fair',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_CONTACTED => 'Contacted',
            self::STATUS_FOLLOWUP => 'Follow-up',
            self::STATUS_CONVERTED => 'Converted',
            self::STATUS_NOT_INTERESTED => 'Not Interested',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Program::class, 'program_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Course::class, 'course_id');
    }

    public function assignedCounselor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_counselor_id');
    }

    public function convertedLead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'converted_lead_id');
    }

    public function followups(): HasMany
    {
        return $this->hasMany(CrmFollowup::class, 'inquiry_id');
    }
}
