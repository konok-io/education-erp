<?php

declare(strict_types=1);

namespace App\Http\Resources\Payment;

use App\Http\Resources\BaseResource;

class InvoiceResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'invoice_no' => $this->invoice_no,
            'total_amount' => $this->total_amount,
            'discount_amount' => $this->discount_amount,
            'fine_amount' => $this->fine_amount,
            'waiver_amount' => $this->waiver_amount,
            'net_amount' => $this->net_amount,
            'paid_amount' => $this->paid_amount,
            'due_amount' => $this->due_amount,
            'due_date' => $this->due_date?->toDateString(),
            'status' => $this->status,
            'billing_month' => $this->billing_month,
            'billing_year' => $this->billing_year,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at?->toIso8601String(),

            'student' => $this->whenLoaded('student', fn() => [
                'id' => $this->student?->uuid,
                'student_no' => $this->student?->student_no,
                'name' => $this->student?->profile?->full_name,
                'class' => $this->student?->class?->name,
            ]),

            'category' => $this->whenLoaded('category', fn() => [
                'id' => $this->category?->uuid,
                'name' => $this->category?->name,
            ]),

            'session' => $this->whenLoaded('session', fn() => [
                'id' => $this->session?->uuid,
                'title' => $this->session?->title,
            ]),

            'payments' => $this->whenLoaded('payments', fn() =>
                $this->payments->map(fn($p) => [
                    'id' => $p->uuid,
                    'amount' => $p->amount,
                    'method' => $p->payment_method,
                    'date' => $p->payment_date?->format('Y-m-d H:i'),
                ])
            ),

            'waivers' => $this->whenLoaded('waivers', fn() =>
                $this->waivers->map(fn($w) => [
                    'id' => $w->uuid,
                    'amount' => $w->amount,
                    'type' => $w->waiver_type,
                ])
            ),
        ];
    }
}
