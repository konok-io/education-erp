<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmKnowledgeBase extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'crm_knowledge_base';

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'content',
        'summary',
        'category',
        'type',
        'video_url',
        'thumbnail',
        'tags',
        'related_articles',
        'author_id',
        'is_published',
        'is_featured',
        'requires_authentication',
        'view_count',
        'helpful_count',
        'not_helpful_count',
        'parent_id',
        'order',
    ];

    protected $casts = [
        'tags' => 'array',
        'related_articles' => 'array',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'requires_authentication' => 'boolean',
        'view_count' => 'integer',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'order' => 'integer',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CrmKnowledgeBase::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CrmKnowledgeBase::class, 'parent_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFaqs($query)
    {
        return $query->where('type', 'faq');
    }

    public function scopeTutorials($query)
    {
        return $query->where('type', 'tutorial');
    }

    public function scopePolicies($query)
    {
        return $query->where('type', 'policy');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function markHelpful(): void
    {
        $this->increment('helpful_count');
    }

    public function markNotHelpful(): void
    {
        $this->increment('not_helpful_count');
    }
}
