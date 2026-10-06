<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CouncilProject extends Model
{
    protected $fillable = ['title', 'slug', 'project_type', 'department_id', 'location', 'summary', 'description', 'starts_at', 'expected_completed_at', 'completed_at', 'project_status', 'progress_percent', 'featured_media_id', 'contact_instructions', 'display_order'];

    protected function casts(): array
    {
        return ['starts_at' => 'date', 'expected_completed_at' => 'date', 'completed_at' => 'date', 'published_at' => 'datetime', 'verified_at' => 'datetime'];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_media_id');
    }

    /** @return BelongsToMany<Ward, $this> */
    public function wards(): BelongsToMany
    {
        return $this->belongsToMany(Ward::class, 'council_project_ward');
    }

    /** @return BelongsToMany<Document, $this> */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'council_project_document');
    }

    /** @return HasMany<ProjectUpdate, $this> */
    public function updates(): HasMany
    {
        return $this->hasMany(ProjectUpdate::class)->orderByDesc('update_date');
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('verification_status', 'publishable')->whereNotNull('published_at')->where('published_at', '<=', now())->where(fn (Builder $q) => $q->whereNull('featured_media_id')->orWhereHas('featuredMedia', fn (Builder $media) => $media->where('status', 'active')));
    }
}
