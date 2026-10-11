<?php

namespace App\Models;

use App\Domain\Content\HasTranslations;
use App\Domain\Content\TranslatableContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $album_date
 */
class Gallery extends Model implements TranslatableContent
{
    use HasTranslations;

    public const TRANSLATABLE_FIELDS = ['title', 'summary', 'description', 'location'];

    protected $fillable = ['slug', 'title', 'summary', 'description', 'location', 'album_date', 'cover_media_id', 'department_id', 'is_featured', 'display_order'];

    protected function casts(): array
    {
        return ['album_date' => 'date', 'is_featured' => 'boolean', 'published_at' => 'datetime', 'verified_at' => 'datetime'];
    }

    /** @return BelongsTo<Media, $this> */
    public function coverMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    /** @return BelongsTo<Department, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Ordered album images with per-image captions.
     *
     * @return BelongsToMany<Media, $this, GalleryMedia>
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'gallery_media')
            ->using(GalleryMedia::class)
            ->withPivot(['caption', 'display_order'])
            ->orderBy('gallery_media.display_order')
            ->orderBy('gallery_media.media_id');
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('verification_status', 'publishable')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(fn (Builder $q) => $q->whereNull('cover_media_id')->orWhereHas('coverMedia', fn (Builder $media) => $media->where('status', 'active')));
    }
}
