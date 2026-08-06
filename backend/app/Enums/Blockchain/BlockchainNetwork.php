<?php

declare(strict_types=1);

namespace App\Enums\Blockchain;

enum BlockchainNetwork: string
{
    case ETHEREUM = 'ethereum';
    case POLYGON = 'polygon';
    case SOLANA = 'solana';
    case HYPERLEDGER = 'hyperledger';
    case POLKADOT = 'polkadot';

    public function label(): string
    {
        return match($this) {
            self::ETHEREUM => 'Ethereum',
            self::POLYGON => 'Polygon',
            self::SOLANA => 'Solana',
            self::HYPERLEDGER => 'Hyperledger',
            self::POLKADOT => 'Polkadot',
        };
    }
}
