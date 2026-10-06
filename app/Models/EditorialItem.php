<?php

namespace App\Models;

use App\Domain\Content\HasTranslations;
use App\Domain\Content\TranslatableContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EditorialItem extends Model implements TranslatableContent
{
    use HasTranslations;

    public const TRANSLATABLE_FIELDS = ['title', 'summary', 'body', 'seo_title', 'meta_description'];

    protected $fillable = ['type', 'slug', 'title', 'summary', 'body', 'category', 'featured_media_id', 'department_id', 'expires_at', 'display_order'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'expires_at' => 'date', 'verified_at' => 'datetime'];
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_media_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** @return BelongsToMany<Document, $this> */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'editorial_item_document');
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('verification_status', 'publishable')->whereNotNull('published_at')->where('published_at', '<=', now())->where(fn (Builder $q) => $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()))->where(fn (Builder $q) => $q->whereNull('featured_media_id')->orWhereHas('featuredMedia', fn (Builder $media) => $media->where('status', 'active')));
    }
}
