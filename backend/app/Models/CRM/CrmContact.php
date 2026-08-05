<?php

declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmContact extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_contacts';

    const CONTACT_TYPE_PROSPECTIVE_STUDENT = 'prospective_student';
    const CONTACT_TYPE_STUDENT = 'student';
    const CONTACT_TYPE_GUARDIAN = 'guardian';
    const CONTACT_TYPE_TEACHER = 'teacher';
    const CONTACT_TYPE_STAFF = 'staff';
    const CONTACT_TYPE_VENDOR = 'vendor';
    const CONTACT_TYPE_SUPPLIER = 'supplier';
    const CONTACT_TYPE_ALUMNI = 'alumni';
    const CONTACT_TYPE_VISITOR = 'visitor';
    const CONTACT_TYPE_ORGANIZATION = 'organization';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_BLOCKED = 'blocked';

    protected $fillable = [
        'uuid',
        'contact_no',
        'full_name',
        'photo',
        'contact_type',
        'mobile',
        'alternative_mobile',
        'email',
        'phone',
        'present_address',
        'permanent_address',
        'district',
        'division',
        'country',
        'organization',
        'designation',
        'student_id',
        'guardian_id',
        'employee_id',
        'social_links',
        'tags',
        'notes',
        'status',
    ];

    protected $casts = [
        'social_links' => 'array',
        'tags' => 'array',
    ];

    public static function generateContactNo(): string
    {
        $prefix = 'CNT';
        $year = date('Y');
        $lastContact = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastContact ? ((int) substr($lastContact->contact_no, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public static function contactTypes(): array
    {
        return [
            self::CONTACT_TYPE_PROSPECTIVE_STUDENT => 'Prospective Student',
            self::CONTACT_TYPE_STUDENT => 'Student',
            self::CONTACT_TYPE_GUARDIAN => 'Guardian',
            self::CONTACT_TYPE_TEACHER => 'Teacher',
            self::CONTACT_TYPE_STAFF => 'Staff',
            self::CONTACT_TYPE_VENDOR => 'Vendor',
            self::CONTACT_TYPE_SUPPLIER => 'Supplier',
            self::CONTACT_TYPE_ALUMNI => 'Alumni',
            self::CONTACT_TYPE_VISITOR => 'Visitor',
            self::CONTACT_TYPE_ORGANIZATION => 'Organization',
        ];
    }

    public function student(): HasOne
    {
        return $this->hasOne(\App\Models\Student\Student::class, 'id', 'student_id');
    }

    public function guardian(): HasOne
    {
        return $this->hasOne(\App\Models\Student\Guardian::class, 'id', 'guardian_id');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(\App\Models\Employee\Employee::class, 'id', 'employee_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(CrmLead::class, 'contact_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(CrmTicket::class, 'contact_id');
    }

    public function communications(): HasMany
    {
        return $this->hasMany(CrmCommunication::class, 'contact_id');
    }
}
