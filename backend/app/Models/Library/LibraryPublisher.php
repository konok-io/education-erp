<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryPublisher extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'library_publishers';

    protected $fillable = [
        'uuid', 'publisher_code', 'publisher_name', 'description', 'address',
        'city', 'country', 'phone', 'email', 'website', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function books(): HasMany
    {
        return $this->hasMany(LibraryBook::class, 'publisher_id');
    }

    // ===================== METHODS =====================

    public static function generatePublisherCode(): string
    {
        return 'PUB-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }
}
