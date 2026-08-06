<?php

declare(strict_types=1);

namespace App\Enums\Backup;

enum ReplicationMode: string
{
    case MASTER_SLAVE = 'master_slave';
    case MASTER_MASTER = 'master_master';
    case CLUSTER = 'cluster';
    case READ_REPLICA = 'read_replica';
    case CONTINUOUS = 'continuous';
    case ASYNC = 'async';
    case SYNC = 'sync';

    public function label(): string
    {
        return match ($this) {
            self::MASTER_SLAVE => 'Master-Slave',
            self::MASTER_MASTER => 'Master-Master',
            self::CLUSTER => 'Cluster',
            self::READ_REPLICA => 'Read Replica',
            self::CONTINUOUS => 'Continuous',
            self::ASYNC => 'Asynchronous',
            self::SYNC => 'Synchronous',
        };
    }
}
