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
