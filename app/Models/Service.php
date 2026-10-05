<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = ['slug', 'name', 'summary', 'description', 'requirements', 'steps', 'fees_information', 'department_id', 'display_order', 'seo_title', 'meta_description'];

    protected function casts(): array
    {
        return ['requirements' => 'array', 'steps' => 'array', 'published_at' => 'datetime'];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('verification_status', 'publishable')->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
