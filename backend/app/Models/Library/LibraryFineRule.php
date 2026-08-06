<?php

declare(strict_types=1);

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryFineRule extends Model
{
    use HasFactory;

    protected $table = 'library_fine_rules';

    protected $fillable = [
        'uuid',
        'name',
        'member_type',
        'fine_type',
        'amount',
        'max_days',
        'max_fine',
        'grace_period',
        'is_active',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'max_fine' => 'decimal:2',
        'grace_period' => 'integer',
        'max_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function getRuleForMember(LibraryMember $member, string $fineType = 'overdue'): ?self
    {
        return self::where('is_active', true)
            ->where(function ($query) use ($member, $fineType) {
                $query->where('member_type', $member->member_type)
                    ->orWhere('member_type', 'all');
            })
            ->where('fine_type', $fineType)
            ->first();
    }

    public function calculateFine(int $daysOverdue): float
    {
        $effectiveDays = max(0, $daysOverdue - $this->grace_period);

        if ($this->max_days > 0 && $effectiveDays > $this->max_days) {
            $effectiveDays = $this->max_days;
        }

        $fine = $effectiveDays * (float) $this->amount;

        if ($this->max_fine > 0 && $fine > $this->max_fine) {
            return (float) $this->max_fine;
        }

        return $fine;
    }
}
