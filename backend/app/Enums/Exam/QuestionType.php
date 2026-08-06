<?php

declare(strict_types=1);

namespace App\Enums\Exam;

enum QuestionType: string
{
    case MCQ = 'mcq';
    case CQ = 'cq';
    case WRITTEN = 'written';
    case SHORT_QUESTION = 'short_question';
    case TRUE_FALSE = 'true_false';
    case FILL_IN_BLANK = 'fill_in_blank';
    case MATCHING = 'matching';
    case PROGRAMMING = 'programming';
    case MATHEMATICS = 'mathematics';
    case DIAGRAM_BASED = 'diagram_based';

    public function label(): string
    {
        return match($this) {
            self::MCQ => 'MCQ',
            self::CQ => 'Creative Question',
            self::WRITTEN => 'Written',
            self::SHORT_QUESTION => 'Short Question',
            self::TRUE_FALSE => 'True/False',
            self::FILL_IN_BLANK => 'Fill in the Blank',
            self::MATCHING => 'Matching',
            self::PROGRAMMING => 'Programming',
            self::MATHEMATICS => 'Mathematics',
            self::DIAGRAM_BASED => 'Diagram Based',
        };
    }

    public function isAutoEvaluable(): bool
    {
        return in_array($this, [
            self::MCQ,
            self::TRUE_FALSE,
            self::FILL_IN_BLANK,
            self::MATCHING,
        ]);
    }

    public function requiresManualEvaluation(): bool
    {
        return !$this->isAutoEvaluable();
    }
}
