<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Official extends Model
{
    protected $fillable = ['name', 'title', 'slug', 'biography', 'photo_media_id', 'department_id', 'display_order'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'verified_at' => 'datetime'];
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'photo_media_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('verification_status', 'publishable')->whereNotNull('published_at')->where('published_at', '<=', now())->where(fn (Builder $q) => $q->whereNull('photo_media_id')->orWhereHas('photo', fn (Builder $photo) => $photo->where('status', 'active')));
    }
}
