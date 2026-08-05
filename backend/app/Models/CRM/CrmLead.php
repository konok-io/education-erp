<?php

declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmLead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_leads';

    const SOURCE_WEBSITE = 'website';
    const SOURCE_FACEBOOK = 'facebook';
    const SOURCE_GOOGLE = 'google';
    const SOURCE_WHATSAPP = 'whatsapp';
    const SOURCE_PHONE = 'phone_call';
    const SOURCE_WALKIN = 'walkin';
    const SOURCE_REFERRAL = 'referral';
    const SOURCE_CAMPAIGN = 'campaign';
    const SOURCE_EDUCATION_FAIR = 'education_fair';

    const STAGE_NEW = 'new';
    const STAGE_CONTACTED = 'contacted';
    const STAGE_INTERESTED = 'interested';
    const STAGE_COUNSELING = 'counseling';
    const STAGE_APPLICATION = 'application';
    const STAGE_ADMISSION = 'admission';
    const STAGE_REJECTED = 'rejected';
    const STAGE_LOST = 'lost';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';
    const PRIORITY_CRITICAL = 'critical';

    const STATUS_ACTIVE = 'active';
    const STATUS_CONVERTED = 'converted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_LOST = 'lost';

    protected $fillable = [
        'uuid',
        'lead_no',
        'contact_id',
        'full_name',
        'mobile',
        'email',
        'lead_source',
        'course_interested',
        'session',
        'assigned_counselor_id',
        'priority',
        'pipeline_stage',
        'lead_score',
        'expected_admission_date',
        'notes',
        'last_discussion',
        'last_followup',
        'next_followup',
        'status',
        'converted_to_student_id',
        'converted_at',
    ];

    protected $casts = [
        'lead_score' => 'integer',
        'expected_admission_date' => 'date',
        'last_followup' => 'date',
        'next_followup' => 'date',
        'converted_at' => 'datetime',
    ];

    public static function generateLeadNo(): string
    {
        $prefix = 'LEAD';
        $year = date('Y');
        $lastLead = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastLead ? ((int) substr($lastLead->lead_no, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public static function leadSources(): array
    {
        return [
            self::SOURCE_WEBSITE => 'Website',
            self::SOURCE_FACEBOOK => 'Facebook',
            self::SOURCE_GOOGLE => 'Google',
            self::SOURCE_WHATSAPP => 'WhatsApp',
            self::SOURCE_PHONE => 'Phone Call',
            self::SOURCE_WALKIN => 'Walk-in',
            self::SOURCE_REFERRAL => 'Referral',
            self::SOURCE_CAMPAIGN => 'Campaign',
            self::SOURCE_EDUCATION_FAIR => 'Education Fair',
        ];
    }

    public static function pipelineStages(): array
    {
        return [
            self::STAGE_NEW => 'New',
            self::STAGE_CONTACTED => 'Contacted',
            self::STAGE_INTERESTED => 'Interested',
            self::STAGE_COUNSELING => 'Counseling',
            self::STAGE_APPLICATION => 'Application',
            self::STAGE_ADMISSION => 'Admission',
            self::STAGE_REJECTED => 'Rejected',
            self::STAGE_LOST => 'Lost',
        ];
    }

    public static function priorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
            self::PRIORITY_CRITICAL => 'Critical',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'contact_id');
    }

    public function assignedCounselor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_counselor_id');
    }

    public function convertedStudent(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student\Student::class, 'converted_to_student_id');
    }

    public function followups(): HasMany
    {
        return $this->hasMany(CrmFollowup::class, 'lead_id');
    }

    public function counselingRecords(): HasMany
    {
        return $this->hasMany(CrmCounselingRecord::class, 'lead_id');
    }

    public function communications(): HasMany
    {
        return $this->hasMany(CrmCommunication::class, 'lead_id');
    }

    public function updateLeadScore(): void
    {
        $score = 0;
        if ($this->last_followup) $score += 20;
        if ($this->next_followup) $score += 10;
        if ($this->pipeline_stage === self::STAGE_CONTACTED) $score += 15;
        if ($this->pipeline_stage === self::STAGE_INTERESTED) $score += 25;
        if ($this->pipeline_stage === self::STAGE_COUNSELING) $score += 30;
        if ($this->pipeline_stage === self::STAGE_APPLICATION) $score += 40;
        if ($this->pipeline_stage === self::STAGE_ADMISSION) $score += 50;
        $this->update(['lead_score' => $score]);
    }
}
