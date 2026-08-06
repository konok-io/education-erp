<?php

declare(strict_types=1);

namespace App\Models\Convocation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Convocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'convocations';

    const STATUS_PLANNING = 'planning';
    const STATUS_REGISTRATION = 'registration';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'uuid',
        'convocation_no',
        'name',
        'name_bn',
        'year',
        'semester',
        'ceremony_date',
        'start_time',
        'end_time',
        'venue',
        'address',
        'chief_guest',
        'special_guest',
        'guest_speaker',
        'agenda',
        'expected_attendees',
        'registered_attendees',
        'registration_fee',
        'description',
        'status',
    ];

    protected $casts = [
        'ceremony_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'expected_attendees' => 'integer',
        'registered_attendees' => 'integer',
        'registration_fee' => 'decimal:2',
    ];

    public static function generateConvocationNo(): string
    {
        $prefix = 'CONV';
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $sequence = $last ? ((int) substr($last->convocation_no, -4)) + 1 : 1;
        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ConvocationRegistration::class, 'convocation_id');
    }

    public function isRegistrationOpen(): bool
    {
        return $this->status === self::STATUS_REGISTRATION;
    }

    public function isUpcoming(): bool
    {
        return $this->ceremony_date->gte(now()->toDateString());
    }

    public function updateRegistrationCount(): void
    {
        $this->update([
            'registered_attendees' => $this->registrations()->where('status', '!=', 'cancelled')->count(),
        ]);
    }
}
