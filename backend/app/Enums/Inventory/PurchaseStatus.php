<?php

declare(strict_types=1);

namespace App\Enums\Inventory;

enum PurchaseStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case ORDERED = 'ordered';
    case RECEIVED = 'received';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::ORDERED => 'Ordered',
            self::RECEIVED => 'Received',
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
        };
    }
}
