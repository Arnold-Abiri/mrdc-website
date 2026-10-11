<?php

namespace App\Models;

use App\Domain\Content\HasTranslations;
use App\Domain\Content\TranslatableContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectUpdate extends Model implements TranslatableContent
{
    use HasTranslations;

    public const TRANSLATABLE_FIELDS = ['title', 'summary'];

    protected $fillable = ['council_project_id', 'update_date', 'title', 'summary', 'progress_percent', 'media_id', 'display_order'];

    protected function casts(): array
    {
        return ['update_date' => 'date', 'published_at' => 'datetime'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(CouncilProject::class, 'council_project_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
