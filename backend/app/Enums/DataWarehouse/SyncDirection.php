<?php

declare(strict_types=1);

namespace App\Enums\DataWarehouse;

enum SyncDirection: string
{
    case SOURCE_TO_TARGET = 'source_to_target';
    case TARGET_TO_SOURCE = 'target_to_source';
    case BIDIRECTIONAL = 'bidirectional';

    public function label(): string
    {
        return match($this) {
            self::SOURCE_TO_TARGET => 'Source to Target',
            self::TARGET_TO_SOURCE => 'Target to Source',
            self::BIDIRECTIONAL => 'Bidirectional',
        };
    }
}
