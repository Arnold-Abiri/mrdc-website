<?php

namespace App\Models;

use App\Domain\Content\HasTranslations;
use App\Domain\Content\TranslatableContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentOpportunity extends Model implements TranslatableContent
{
    use HasTranslations;

    public const TRANSLATABLE_FIELDS = ['title', 'summary', 'description', 'location'];

    protected $fillable = ['slug', 'title', 'sector', 'summary', 'description', 'location', 'opportunity_status', 'document_id', 'department_id', 'display_order'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'verified_at' => 'datetime'];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('verification_status', 'publishable')->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
