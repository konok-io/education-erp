<?php

declare(strict_types=1);

namespace App\Models\Research;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journal extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'journals';

    protected $fillable = [
        'uuid', 'journal_name', 'issn', 'e_issn', 'publisher', 'country',
        'website', 'email', 'description', 'impact_factor', 'quartile',
        'category', 'is_indexed_scopus', 'is_indexed_wos', 'is_indexed_pubmed',
        'frequency', 'apc', 'apc_currency', 'is_active',
    ];

    protected $casts = [
        'impact_factor' => 'decimal:2',
        'apc' => 'decimal:2',
        'is_indexed_scopus' => 'boolean',
        'is_indexed_wos' => 'boolean',
        'is_indexed_pubmed' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===================== QUARTILES =====================
    public const Q1 = 'Q1';
    public const Q2 = 'Q2';
    public const Q3 = 'Q3';
    public const Q4 = 'Q4';

    // ===================== METHODS =====================

    public static function quartiles(): array
    {
        return [
            self::Q1 => 'Q1',
            self::Q2 => 'Q2',
            self::Q3 => 'Q3',
            self::Q4 => 'Q4',
        ];
    }
}
