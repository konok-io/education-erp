<?php

declare(strict_types=1);

namespace App\DTO\Inventory;

use App\Enums\Inventory\PurchaseStatus;
use Illuminate\Http\Request;

final class PurchaseDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $purchase_no,
        public readonly string $supplier_uuid,
        public readonly \DateTimeInterface $purchase_date,
        public readonly float $total_amount,
        public readonly ?float $discount_amount,
        public readonly float $net_amount,
        public readonly ?float $tax_amount,
        public readonly PurchaseStatus $status = PurchaseStatus::PENDING,
        public readonly ?string $invoice_no,
        public readonly ?string $invoice_path,
        public readonly ?string $payment_method,
        public readonly string $payment_status = 'unpaid',
        public readonly ?string $notes,
        public readonly ?string $created_by,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            purchase_no: $request->input('purchase_no'),
            supplier_uuid: $request->input('supplier_uuid'),
            purchase_date: new \DateTime($request->input('purchase_date')),
            total_amount: (float) $request->input('total_amount'),
            discount_amount: $request->input('discount_amount') ? (float) $request->input('discount_amount') : null,
            net_amount: (float) $request->input('net_amount'),
            tax_amount: $request->input('tax_amount') ? (float) $request->input('tax_amount') : null,
            status: PurchaseStatus::tryFrom($request->input('status', 'pending')) ?? PurchaseStatus::PENDING,
            invoice_no: $request->input('invoice_no'),
            invoice_path: $request->input('invoice_path'),
            payment_method: $request->input('payment_method'),
            payment_status: $request->input('payment_status', 'unpaid'),
            notes: $request->input('notes'),
            created_by: $request->input('created_by'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'purchase_no' => $this->purchase_no,
            'supplier_uuid' => $this->supplier_uuid,
            'purchase_date' => $this->purchase_date->format('Y-m-d'),
            'total_amount' => $this->total_amount,
            'discount_amount' => $this->discount_amount,
            'net_amount' => $this->net_amount,
            'tax_amount' => $this->tax_amount,
            'status' => $this->status->value,
            'invoice_no' => $this->invoice_no,
            'invoice_path' => $this->invoice_path,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
        ];
    }
}
