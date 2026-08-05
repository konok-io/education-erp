<?php

declare(strict_types=1);

namespace App\Http\Resources\Admission;

use App\Http\Resources\BaseResource;

class ApplicationResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'application_no' => $this->application_no,
            'applicant_name' => $this->applicant_name,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'gender' => $this->gender,
            'religion' => $this->religion,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'ssc_gpa' => $this->ssc_gpa,
            'hsc_gpa' => $this->hsc_gpa,
            'quota' => $this->quota,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'merit_position' => $this->merit_position,
            'is_waiting' => $this->is_waiting,
            'waiting_position' => $this->waiting_position,
            'interview_date' => $this->interview_date?->toDateString(),
            'interview_time' => $this->interview_time,
            'interview_venue' => $this->interview_venue,
            'submitted_at' => $this->submitted_at?->toIso8601String(),

            'campaign' => $this->whenLoaded('campaign', fn() => [
                'id' => $this->campaign?->uuid,
                'title' => $this->campaign?->title,
            ]),

            'documents' => $this->whenLoaded('documents', fn() =>
                $this->documents->map(fn($d) => [
                    'id' => $d->uuid,
                    'type' => $d->document_type,
                    'name' => $d->document_name,
                    'is_verified' => $d->is_verified,
                ])
            ),

            'payments' => $this->whenLoaded('payments', fn() =>
                $this->payments->map(fn($p) => [
                    'id' => $p->uuid,
                    'amount' => $p->amount,
                    'method' => $p->payment_method,
                    'status' => $p->status,
                ])
            ),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
