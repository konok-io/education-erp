<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionCategory extends Model
{
    use HasFactory;

    protected $table = 'question_categories';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'parent_id',
        'order',
        'description',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(QuestionCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(QuestionCategory::class, 'parent_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'category_id');
    }
}
