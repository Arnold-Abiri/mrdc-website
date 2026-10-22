<?php

namespace App\Models;

use App\Domain\Content\HasTranslations;
use App\Domain\Content\TranslatableContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $slug
 * @property string $title
 * @property string|null $description
 * @property string $category
 * @property int $download_count
 * @property int $current_version
 * @property Carbon|null $published_at
 * @property Carbon|null $reference_date
 * @property Media|null $media
 * @property Department|null $department
 */
class Document extends Model implements TranslatableContent
{
    use HasTranslations;

    public const TRANSLATABLE_FIELDS = ['title', 'description'];

    public const CATEGORIES = ['policy', 'bylaw', 'report', 'financial_statement', 'financial_report', 'plan', 'strategic_plan', 'budget', 'procurement_plan', 'awards_register', 'form', 'notice', 'minutes', 'agenda', 'schedule', 'organogram', 'publication', 'other'];

    public const FINANCIAL_CATEGORIES = ['budget', 'financial_statement', 'financial_report', 'procurement_plan', 'awards_register'];

    public const GOVERNANCE_CATEGORIES = ['bylaw', 'policy', 'strategic_plan', 'plan', 'minutes', 'agenda', 'schedule', 'organogram'];

    protected $fillable = ['slug', 'title', 'description', 'category', 'media_id', 'department_id', 'visibility', 'reference_date'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'reference_date' => 'date', 'verified_at' => 'datetime'];
    }

    /** @return BelongsTo<Media, $this> */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /** @return BelongsTo<Department, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** @return HasMany<DocumentVersion, $this> */
    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class)->orderByDesc('version_number');
    }

    public function referenceYear(): ?int
    {
        $raw = $this->getAttributes()['reference_date'] ?? null;
        if (! is_string($raw) || strlen($raw) < 4 || ! is_numeric(substr($raw, 0, 4))) {
            return null;
        }

        return (int) substr($raw, 0, 4);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('visibility', 'public')
            ->where('verification_status', 'publishable')->where('published_at', '<=', now())
            ->whereHas('media', fn (Builder $media) => $media->where('status', 'active'));
    }
}
