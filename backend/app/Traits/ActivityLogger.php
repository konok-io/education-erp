<?php

declare(strict_types=1);

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;

trait ActivityLogger
{
    use LogsActivity;

    /**
     * Log only changed attributes.
     */
    protected $logOnlyDirty = true;

    /**
     * Log fillable attributes.
     */
    protected $logFillable = true;

    /**
     * Log attributes to ignore.
     */
    protected array $logAttributesToIgnore = [
        'password',
        'remember_token',
    ];

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): \Spatie\Activitylog\ActivitylogOptions
    {
        return \Spatie\Activitylog\ActivitylogOptions::defaults()
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly([
                'updated_at',
                'remember_token',
            ]);
    }
}
