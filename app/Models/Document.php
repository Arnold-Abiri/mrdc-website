<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = ['slug', 'title', 'description', 'category', 'media_id', 'department_id', 'visibility', 'reference_date'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'reference_date' => 'date', 'verified_at' => 'datetime'];
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('visibility', 'public')
            ->where('verification_status', 'publishable')->where('published_at', '<=', now())
            ->whereHas('media', fn (Builder $media) => $media->where('status', 'active'));
    }
}
