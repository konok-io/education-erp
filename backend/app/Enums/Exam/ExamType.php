<?php

declare(strict_types=1);

namespace App\Enums\Exam;

enum ExamType: string
{
    case CLASS_TEST = 'class_test';
    case QUIZ = 'quiz';
    case ASSIGNMENT = 'assignment';
    case MID_TERM = 'mid_term';
    case FINAL_EXAM = 'final_exam';
    case MODEL_TEST = 'model_test';
    case ADMISSION_TEST = 'admission_test';
    case PRACTICAL = 'practical';
    case VIVA = 'viva';
    case IMPROVEMENT = 'improvement';
    case SUPPLEMENTARY = 'supplementary';

    public function label(): string
    {
        return match($this) {
            self::CLASS_TEST => 'Class Test',
            self::QUIZ => 'Quiz',
            self::ASSIGNMENT => 'Assignment',
            self::MID_TERM => 'Mid Term',
            self::FINAL_EXAM => 'Final Exam',
            self::MODEL_TEST => 'Model Test',
            self::ADMISSION_TEST => 'Admission Test',
            self::PRACTICAL => 'Practical',
            self::VIVA => 'Viva',
            self::IMPROVEMENT => 'Improvement',
            self::SUPPLEMENTARY => 'Supplementary',
        };
    }

    public function isPractical(): bool
    {
        return in_array($this, [self::PRACTICAL, self::VIVA]);
    }

    public function isTheory(): bool
    {
        return !in_array($this, [self::PRACTICAL, self::VIVA]);
    }
}
