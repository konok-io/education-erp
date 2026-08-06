<?php

declare(strict_types=1);

namespace App\Enums\LMS;

enum ContentType: string
{
    case VIDEO = 'video';
    case PDF = 'pdf';
    case DOCUMENT = 'document';
    case IMAGE = 'image';
    case AUDIO = 'audio';
    case LINK = 'link';
    case QUIZ = 'quiz';
    case ASSIGNMENT = 'assignment';
    case LIVE_CLASS = 'live_class';

    public function label(): string
    {
        return match($this) {
            self::VIDEO => 'Video',
            self::PDF => 'PDF',
            self::DOCUMENT => 'Document',
            self::IMAGE => 'Image',
            self::AUDIO => 'Audio',
            self::LINK => 'External Link',
            self::QUIZ => 'Quiz',
            self::ASSIGNMENT => 'Assignment',
            self::LIVE_CLASS => 'Live Class',
        };
    }
}
