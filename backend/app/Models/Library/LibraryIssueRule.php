<?php

declare(strict_types=1);

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryIssueRule extends Model
{
    use HasFactory;

    protected $table = 'library_issue_rules';

    protected $fillable = [
        'uuid',
        'name',
        'member_type',
        'category_id',
        'max_books',
        'max_days',
        'max_renewals',
        'allow_reservation',
        'is_active',
        'description',
    ];

    protected $casts = [
        'max_books' => 'integer',
        'max_days' => 'integer',
        'max_renewals' => 'integer',
        'allow_reservation' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function getRuleForMember(LibraryMember $member, ?int $categoryId = null): ?self
    {
        return self::where('is_active', true)
            ->where(function ($query) use ($member, $categoryId) {
                $query->where('member_type', $member->member_type)
                    ->orWhere('member_type', 'all');
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where(function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId)
                        ->orWhereNull('category_id');
                });
            }, function ($query) {
                $query->whereNull('category_id');
            })
            ->first();
    }

    public static function getDefaultRule(string $memberType): array
    {
        return match ($memberType) {
            LibraryMember::TYPE_TEACHER => [
                'max_books' => 20,
                'max_days' => 90,
                'max_renewals' => 3,
                'allow_reservation' => true,
            ],
            LibraryMember::TYPE_EMPLOYEE => [
                'max_books' => 10,
                'max_days' => 30,
                'max_renewals' => 2,
                'allow_reservation' => true,
            ],
            LibraryMember::TYPE_RESEARCHER => [
                'max_books' => 15,
                'max_days' => 60,
                'max_renewals' => 2,
                'allow_reservation' => true,
            ],
            LibraryMember::TYPE_GUEST => [
                'max_books' => 2,
                'max_days' => 14,
                'max_renewals' => 1,
                'allow_reservation' => false,
            ],
            default => [
                'max_books' => 5,
                'max_days' => 14,
                'max_renewals' => 2,
                'allow_reservation' => true,
            ],
        };
    }
}
