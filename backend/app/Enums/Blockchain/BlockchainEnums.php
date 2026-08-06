<?php

declare(strict_types=1);

namespace App\Enums\Blockchain;

enum CredentialStatus: string
{
    case ISSUED = 'issued';
    case VERIFIED = 'verified';
    case REVOKED = 'revoked';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match($this) {
            self::ISSUED => 'Issued',
            self::VERIFIED => 'Verified',
            self::REVOKED => 'Revoked',
            self::EXPIRED => 'Expired',
        };
    }
}

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
