<?php

declare(strict_types=1);

namespace App\Enums\Exam;

enum DifficultyLevel: string
{
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';
    case EXPERT = 'expert';

    public function label(): string
    {
        return match($this) {
            self::EASY => 'Easy',
            self::MEDIUM => 'Medium',
            self::HARD => 'Hard',
            self::EXPERT => 'Expert',
        };
    }

    public function weight(): int
    {
        return match($this) {
            self::EASY => 1,
            self::MEDIUM => 2,
            self::HARD => 3,
            self::EXPERT => 4,
        };
    }
}
