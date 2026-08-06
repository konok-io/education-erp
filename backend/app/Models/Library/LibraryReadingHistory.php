<?php

declare(strict_types=1);

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryReadingHistory extends Model
{
    use HasFactory;

    protected $table = 'library_reading_history';

    const ACCESS_ISSUE = 'issue';
    const ACCESS_RETURN = 'return';
    const ACCESS_READ = 'read';
    const ACCESS_DOWNLOAD = 'download';
    const ACCESS_BROWSE = 'browse';

    protected $fillable = [
        'uuid',
        'member_id',
        'book_id',
        'book_copy_id',
        'access_time',
        'access_type',
        'ip_address',
        'notes',
    ];

    protected $casts = [
        'access_time' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(LibraryMember::class, 'member_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(LibraryBook::class, 'book_id');
    }

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(LibraryBookCopy::class, 'book_copy_id');
    }

    public static function recordAccess(
        int $memberId,
        ?int $bookId = null,
        ?int $bookCopyId = null,
        string $accessType = self::ACCESS_BROWSE,
        ?string $ipAddress = null,
        ?string $notes = null
    ): self {
        return self::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'member_id' => $memberId,
            'book_id' => $bookId,
            'book_copy_id' => $bookCopyId,
            'access_time' => now(),
            'access_type' => $accessType,
            'ip_address' => $ipAddress,
            'notes' => $notes,
        ]);
    }
}
