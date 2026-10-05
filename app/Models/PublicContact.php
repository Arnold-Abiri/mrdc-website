<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicContact extends Model
{
    protected $fillable = ['office', 'type', 'value', 'department_id', 'is_public', 'display_order'];

    protected function casts(): array
    {
        return ['is_public' => 'boolean', 'published_at' => 'datetime'];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true)->where('status', 'published')->where('verification_status', 'publishable')->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
