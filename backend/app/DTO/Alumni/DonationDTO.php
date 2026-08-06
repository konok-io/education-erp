<?php

declare(strict_types=1);

namespace App\DTO\Alumni;

use App\Enums\Alumni\DonationType;
use Illuminate\Http\Request;

final class DonationDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $donation_id,
        public readonly string $alumni_uuid,
        public readonly float $amount,
        public readonly DonationType $type = DonationType::ONE_TIME,
        public readonly ?string $currency = 'BDT',
        public readonly ?string $payment_method,
        public readonly ?string $transaction_id,
        public readonly ?string $payment_date,
        public readonly ?string $campaign_uuid,
        public readonly ?string $purpose,
        public readonly ?string $notes,
        public readonly string $status = 'pending',
        public readonly ?string $receipt_path,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            donation_id: $request->input('donation_id'),
            alumni_uuid: $request->input('alumni_uuid'),
            amount: (float) $request->input('amount'),
            type: DonationType::tryFrom($request->input('type', 'one_time')) ?? DonationType::ONE_TIME,
            currency: $request->input('currency', 'BDT'),
            payment_method: $request->input('payment_method'),
            transaction_id: $request->input('transaction_id'),
            payment_date: $request->input('payment_date'),
            campaign_uuid: $request->input('campaign_uuid'),
            purpose: $request->input('purpose'),
            notes: $request->input('notes'),
            status: $request->input('status', 'pending'),
            receipt_path: $request->input('receipt_path'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'donation_id' => $this->donation_id,
            'alumni_uuid' => $this->alumni_uuid,
            'amount' => $this->amount,
            'type' => $this->type->value,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'transaction_id' => $this->transaction_id,
            'payment_date' => $this->payment_date,
            'campaign_uuid' => $this->campaign_uuid,
            'purpose' => $this->purpose,
            'notes' => $this->notes,
            'status' => $this->status,
            'receipt_path' => $this->receipt_path,
        ];
    }
}
