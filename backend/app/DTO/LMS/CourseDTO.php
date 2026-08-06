<?php

declare(strict_types=1);

namespace App\DTO\LMS;

use App\Enums\LMS\CourseStatus;
use Illuminate\Http\Request;

final class CourseDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $course_code,
        public readonly string $title,
        public readonly ?string $title_bn,
        public readonly string $description,
        public readonly ?string $instructor_uuid,
        public readonly ?string $category_uuid,
        public readonly ?string $thumbnail,
        public readonly ?string $syllabus,
        public readonly ?int $duration_hours,
        public readonly string $difficulty_level,
        public readonly bool $is_free = false,
        public readonly ?float $price,
        public readonly ?string $currency,
        public readonly CourseStatus $status = CourseStatus::DRAFT,
        public readonly bool $is_certified = false,
        public readonly ?string $certificate_template,
        public readonly ?string $prerequisites,
        public readonly ?string $tags,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            course_code: $request->input('course_code'),
            title: $request->input('title'),
            title_bn: $request->input('title_bn'),
            description: $request->input('description'),
            instructor_uuid: $request->input('instructor_uuid'),
            category_uuid: $request->input('category_uuid'),
            thumbnail: $request->input('thumbnail'),
            syllabus: $request->input('syllabus'),
            duration_hours: $request->input('duration_hours') ? (int) $request->input('duration_hours') : null,
            difficulty_level: $request->input('difficulty_level', 'beginner'),
            is_free: (bool) $request->input('is_free', false),
            price: $request->input('price') ? (float) $request->input('price') : null,
            currency: $request->input('currency', 'BDT'),
            status: CourseStatus::tryFrom($request->input('status', 'draft')) ?? CourseStatus::DRAFT,
            is_certified: (bool) $request->input('is_certified', false),
            certificate_template: $request->input('certificate_template'),
            prerequisites: $request->input('prerequisites'),
            tags: $request->input('tags'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'course_code' => $this->course_code,
            'title' => $this->title,
            'title_bn' => $this->title_bn,
            'description' => $this->description,
            'instructor_uuid' => $this->instructor_uuid,
            'category_uuid' => $this->category_uuid,
            'thumbnail' => $this->thumbnail,
            'syllabus' => $this->syllabus,
            'duration_hours' => $this->duration_hours,
            'difficulty_level' => $this->difficulty_level,
            'is_free' => $this->is_free,
            'price' => $this->price,
            'currency' => $this->currency,
            'status' => $this->status->value,
            'is_certified' => $this->is_certified,
            'certificate_template' => $this->certificate_template,
            'prerequisites' => $this->prerequisites,
            'tags' => $this->tags,
        ];
    }
}
