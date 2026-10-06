<?php

namespace App\Models;

use App\Domain\Content\HasTranslations;
use App\Domain\Content\TranslatableContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model implements TranslatableContent
{
    use HasTranslations;

    public const TRANSLATABLE_FIELDS = ['title', 'summary', 'seo_title', 'meta_description'];

    protected $fillable = ['slug', 'title', 'summary', 'blocks', 'seo_title', 'meta_description', 'department_id'];

    protected function casts(): array
    {
        return ['blocks' => 'array', 'published_at' => 'datetime'];
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(PageRevision::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
