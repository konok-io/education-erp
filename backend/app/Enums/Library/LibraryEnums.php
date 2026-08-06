<?php

declare(strict_types=1);

namespace App\Enums\Library;

enum BookType: string
{
    case BOOK = 'book';
    case JOURNAL = 'journal';
    case MAGAZINE = 'magazine';
    case NEWSPAPER = 'newspaper';
    case THESIS = 'thesis';
    case DISSERTATION = 'dissertation';
    case E_BOOK = 'e_book';
    case AUDIO = 'audio';
    case VIDEO = 'video';
    case REFERENCE = 'reference';

    public function label(): string
    {
        return match($this) {
            self::BOOK => 'Book',
            self::JOURNAL => 'Journal',
            self::MAGAZINE => 'Magazine',
            self::NEWSPAPER => 'Newspaper',
            self::THESIS => 'Thesis',
            self::DISSERTATION => 'Dissertation',
            self::E_BOOK => 'E-Book',
            self::AUDIO => 'Audio',
            self::VIDEO => 'Video',
            self::REFERENCE => 'Reference',
        };
    }
}

enum BorrowingStatus: string
{
    case BORROWED = 'borrowed';
    case RETURNED = 'returned';
    case OVERDUE = 'overdue';
    case LOST = 'lost';
    case DAMAGED = 'damaged';

    public function label(): string
    {
        return match($this) {
            self::BORROWED => 'Borrowed',
            self::RETURNED => 'Returned',
            self::OVERDUE => 'Overdue',
            self::LOST => 'Lost',
            self::DAMAGED => 'Damaged',
        };
    }
}

enum BookStatus: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case BORROWED = 'borrowed';
    case UNDER_MAINTENANCE = 'under_maintenance';
    case LOST = 'lost';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'Available',
            self::RESERVED => 'Reserved',
            self::BORROWED => 'Borrowed',
            self::UNDER_MAINTENANCE => 'Under Maintenance',
            self::LOST => 'Lost',
            self::ARCHIVED => 'Archived',
        };
    }
}
