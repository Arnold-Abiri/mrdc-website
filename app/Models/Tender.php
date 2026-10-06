<?php

namespace App\Models;

use App\Domain\Content\HasTranslations;
use App\Domain\Content\TranslatableContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tender extends Model implements TranslatableContent
{
    use HasTranslations;

    public const TRANSLATABLE_FIELDS = ['title', 'description', 'contact_instructions', 'award_remarks'];

    protected $fillable = ['reference', 'slug', 'title', 'category', 'description', 'opens_at', 'closes_at', 'lifecycle_status', 'contact_instructions', 'document_id', 'department_id', 'display_order', 'award_status', 'awarded_to', 'awarded_at', 'award_amount', 'award_reference', 'award_document_id', 'award_remarks'];

    protected function casts(): array
    {
        return ['opens_at' => 'date', 'closes_at' => 'datetime', 'published_at' => 'datetime', 'verified_at' => 'datetime', 'awarded_at' => 'date', 'award_amount' => 'decimal:2'];
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

    public function isOpen(): bool
    {
        if ($this->lifecycle_status === 'cancelled' || $this->lifecycle_status === 'awarded') {
            return false;
        }
        if ($this->lifecycle_status === 'closed') {
            return false;
        }
        if ($this->closes_at !== null && now()->gt($this->closes_at)) {
            return false;
        }
        if ($this->opens_at !== null && today()->lt($this->opens_at)) {
            return false;
        }

        return true;
    }

    public function displayStatus(): string
    {
        if (! $this->isOpen()) {
            return in_array($this->lifecycle_status, ['awarded', 'cancelled', 'closed'], true) ? $this->lifecycle_status : 'closed';
        }

        return $this->opens_at !== null && today()->lt($this->opens_at) ? 'upcoming' : 'open';
    }
}
