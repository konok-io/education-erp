<?php

declare(strict_types=1);

namespace App\Models\Routine;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'rooms';

    protected $fillable = [
        'uuid',
        'room_number',
        'room_name',
        'building',
        'floor',
        'capacity',
        'current_capacity',
        'room_type',
        'has_projector',
        'has_ac',
        'has_computer',
        'status',
        'description',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'current_capacity' => 'integer',
        'has_projector' => 'boolean',
        'has_ac' => 'boolean',
        'has_computer' => 'boolean',
    ];

    // ===================== ROOM TYPES =====================
    public const TYPE_CLASSROOM = 'classroom';
    public const TYPE_LABORATORY = 'laboratory';
    public const TYPE_COMPUTER_LAB = 'computer_lab';
    public const TYPE_LIBRARY = 'library';
    public const TYPE_SEMINAR = 'seminar_hall';
    public const TYPE_CONFERENCE = 'conference_room';
    public const TYPE_OTHER = 'other';

    public function routines(): HasMany
    {
        return $this->hasMany(Routine::class, 'room_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('room_type', $type);
    }

    public static function roomTypes(): array
    {
        return [
            self::TYPE_CLASSROOM => 'Classroom',
            self::TYPE_LABORATORY => 'Laboratory',
            self::TYPE_COMPUTER_LAB => 'Computer Lab',
            self::TYPE_LIBRARY => 'Library',
            self::TYPE_SEMINAR => 'Seminar Hall',
            self::TYPE_CONFERENCE => 'Conference Room',
            self::TYPE_OTHER => 'Other',
        ];
    }
}
