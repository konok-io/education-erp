<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LibraryMember extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'library_members';

    protected $fillable = [
        'uuid',
        'member_no',
        'member_type',
        'name',
        'email',
        'phone',
        'photo',
        'department',
        'student_id',
        'employee_id',
        'joining_date',
        'expiry_date',
        'status',
        'max_books',
        'max_days',
        'fine_rate',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'expiry_date' => 'date',
        'max_books' => 'integer',
        'max_days' => 'integer',
        'fine_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ===================== MEMBER TYPES =====================
    public const TYPE_STUDENT = 'student';
    public const TYPE_TEACHER = 'teacher';
    public const TYPE_EMPLOYEE = 'employee';
    public const TYPE_RESEARCHER = 'researcher';
    public const TYPE_GUEST = 'guest';

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_CLOSED = 'closed';

    // ===================== RELATIONSHIPS =====================

    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'member_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(BookReservation::class, 'member_id');
    }

    public function fines(): HasMany
    {
        return $this->hasMany(LibraryFine::class, 'member_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', now());
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('member_type', $type);
    }

    // ===================== METHODS =====================

    public static function generateMemberNo(): string
    {
        $prefix = 'LM';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public static function memberTypes(): array
    {
        return [
            self::TYPE_STUDENT => 'Student',
            self::TYPE_TEACHER => 'Teacher',
            self::TYPE_EMPLOYEE => 'Employee',
            self::TYPE_RESEARCHER => 'Researcher',
            self::TYPE_GUEST => 'Guest',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_BLOCKED => 'Blocked',
            self::STATUS_CLOSED => 'Closed',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    public function hasOverdueBooks(): bool
    {
        return $this->issues()
            ->where('status', 'issued')
            ->where('due_date', '<', now())
            ->exists();
    }

    public function getIssuedBooksCount(): int
    {
        return $this->issues()
            ->whereIn('status', ['issued', 'overdue'])
            ->count();
    }

    public function canIssueMoreBooks(): bool
    {
        return $this->getIssuedBooksCount() < $this->max_books;
    }

    public function getUnpaidFinesTotal(): float
    {
        return (float) $this->fines()
            ->where('status', 'pending')
            ->sum('amount');
    }

    public function hasUnpaidFines(): bool
    {
        return $this->getUnpaidFinesTotal() > 0;
    }

    public function block(): void
    {
        $this->update(['status' => self::STATUS_BLOCKED]);
    }

    public function unblock(): void
    {
        $this->update(['status' => self::STATUS_ACTIVE]);
    }
}
