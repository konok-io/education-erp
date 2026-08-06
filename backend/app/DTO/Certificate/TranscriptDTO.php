<?php

declare(strict_types=1);

namespace App\DTO\Certificate;

use Illuminate\Http\Request;

final class TranscriptDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $transcript_number,
        public readonly string $student_uuid,
        public readonly string $academic_year_uuid,
        public readonly ?string $semester_uuid,
        public readonly string $issued_date,
        public readonly ?string $cgpa,
        public readonly ?string $total_credits,
        public readonly ?string $grade = null,
        public readonly ?string $document_path = null,
        public readonly ?string $qr_code = null,
        public readonly ?string $remarks = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            transcript_number: $request->input('transcript_number'),
            student_uuid: $request->input('student_uuid'),
            academic_year_uuid: $request->input('academic_year_uuid'),
            semester_uuid: $request->input('semester_uuid'),
            issued_date: $request->input('issued_date'),
            cgpa: $request->input('cgpa'),
            total_credits: $request->input('total_credits'),
            grade: $request->input('grade'),
            document_path: $request->input('document_path'),
            qr_code: $request->input('qr_code'),
            remarks: $request->input('remarks'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'transcript_number' => $this->transcript_number,
            'student_uuid' => $this->student_uuid,
            'academic_year_uuid' => $this->academic_year_uuid,
            'semester_uuid' => $this->semester_uuid,
            'issued_date' => $this->issued_date,
            'cgpa' => $this->cgpa,
            'total_credits' => $this->total_credits,
            'grade' => $this->grade,
            'document_path' => $this->document_path,
            'qr_code' => $this->qr_code,
            'remarks' => $this->remarks,
        ];
    }
}
