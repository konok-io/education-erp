<?php

declare(strict_types=1);

namespace App\DTO\Exam;

use App\Enums\Exam\DifficultyLevel;
use App\Enums\Exam\QuestionType;
use Illuminate\Http\Request;

final class QuestionDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $question_code,
        public readonly string $question_text,
        public readonly QuestionType $type,
        public readonly DifficultyLevel $difficulty,
        public readonly float $marks,
        public readonly ?string $subject_uuid,
        public readonly ?string $chapter_uuid,
        public readonly ?string $topic_uuid,
        public readonly ?string $question_image,
        public readonly ?array $options,
        public readonly ?array $correct_answer,
        public readonly ?string $explanation,
        public readonly bool $is_active = true,
        public readonly int $times_used = 0,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            question_code: $request->input('question_code'),
            question_text: $request->input('question_text'),
            type: QuestionType::from($request->input('type')),
            difficulty: DifficultyLevel::from($request->input('difficulty', 'medium')),
            marks: (float) $request->input('marks'),
            subject_uuid: $request->input('subject_uuid'),
            chapter_uuid: $request->input('chapter_uuid'),
            topic_uuid: $request->input('topic_uuid'),
            question_image: $request->input('question_image'),
            options: $request->input('options'),
            correct_answer: $request->input('correct_answer'),
            explanation: $request->input('explanation'),
            is_active: (bool) $request->input('is_active', true),
            times_used: (int) $request->input('times_used', 0),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'question_code' => $this->question_code,
            'question_text' => $this->question_text,
            'type' => $this->type->value,
            'difficulty' => $this->difficulty->value,
            'marks' => $this->marks,
            'subject_uuid' => $this->subject_uuid,
            'chapter_uuid' => $this->chapter_uuid,
            'topic_uuid' => $this->topic_uuid,
            'question_image' => $this->question_image,
            'options' => $this->options,
            'correct_answer' => $this->correct_answer,
            'explanation' => $this->explanation,
            'is_active' => $this->is_active,
            'times_used' => $this->times_used,
        ];
    }
}
