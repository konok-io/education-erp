<?php

declare(strict_types=1);

namespace App\Enums\Backup;

enum StorageProviderType: string
{
    case LOCAL = 'local';
    case NAS = 'nas';
    case SAN = 'san';
    case S3 = 's3';
    case AZURE = 'azure';
    case GCS = 'gcs';
    case MINIO = 'minio';
    case FTP = 'ftp';
    case SFTP = 'sftp';

    public function label(): string
    {
        return match ($this) {
            self::LOCAL => 'Local Storage',
            self::NAS => 'Network Attached Storage',
            self::SAN => 'Storage Area Network',
            self::S3 => 'Amazon S3',
            self::AZURE => 'Azure Blob Storage',
            self::GCS => 'Google Cloud Storage',
            self::MINIO => 'MinIO',
            self::FTP => 'FTP',
            self::SFTP => 'SFTP',
        };
    }

    public function isCloud(): bool
    {
        return in_array($this, [self::S3, self::AZURE, self::GCS, self::MINIO]);
    }
}
