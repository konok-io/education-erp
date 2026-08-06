<?php

declare(strict_types=1);

namespace App\DTO\Blockchain;

use App\Enums\Blockchain\BlockchainNetwork;
use App\Enums\Blockchain\CredentialStatus;
use Illuminate\Http\Request;

final class CredentialDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $credential_type,
        public readonly string $holder_uuid,
        public readonly string $holder_type,
        public readonly BlockchainNetwork $network = BlockchainNetwork::ETHEREUM,
        public readonly CredentialStatus $status = CredentialStatus::ISSUED,
        public readonly ?string $wallet_address,
        public readonly ?string $transaction_hash,
        public readonly ?string $block_number,
        public readonly ?string $token_id,
        public readonly ?string $ipfs_hash,
        public readonly ?string $metadata_uri,
        public readonly ?\DateTimeInterface $issued_at,
        public readonly ?\DateTimeInterface $expires_at,
        public readonly ?string $revoked_at,
        public readonly ?string $revocation_reason,
        public readonly ?string $issuer_uuid,
        public readonly ?string $digital_signature,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            credential_type: $request->input('credential_type'),
            holder_uuid: $request->input('holder_uuid'),
            holder_type: $request->input('holder_type'),
            network: BlockchainNetwork::tryFrom($request->input('network', 'ethereum')) ?? BlockchainNetwork::ETHEREUM,
            status: CredentialStatus::tryFrom($request->input('status', 'issued')) ?? CredentialStatus::ISSUED,
            wallet_address: $request->input('wallet_address'),
            transaction_hash: $request->input('transaction_hash'),
            block_number: $request->input('block_number'),
            token_id: $request->input('token_id'),
            ipfs_hash: $request->input('ipfs_hash'),
            metadata_uri: $request->input('metadata_uri'),
            issued_at: $request->input('issued_at') ? new \DateTime($request->input('issued_at')) : null,
            expires_at: $request->input('expires_at') ? new \DateTime($request->input('expires_at')) : null,
            revoked_at: $request->input('revoked_at'),
            revocation_reason: $request->input('revocation_reason'),
            issuer_uuid: $request->input('issuer_uuid'),
            digital_signature: $request->input('digital_signature'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'credential_type' => $this->credential_type,
            'holder_uuid' => $this->holder_uuid,
            'holder_type' => $this->holder_type,
            'network' => $this->network->value,
            'status' => $this->status->value,
            'wallet_address' => $this->wallet_address,
            'transaction_hash' => $this->transaction_hash,
            'block_number' => $this->block_number,
            'token_id' => $this->token_id,
            'ipfs_hash' => $this->ipfs_hash,
            'metadata_uri' => $this->metadata_uri,
            'issued_at' => $this->issued_at?->format('Y-m-d H:i:s'),
            'expires_at' => $this->expires_at?->format('Y-m-d H:i:s'),
            'revoked_at' => $this->revoked_at,
            'revocation_reason' => $this->revocation_reason,
            'issuer_uuid' => $this->issuer_uuid,
            'digital_signature' => $this->digital_signature,
        ];
    }
}
