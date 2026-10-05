<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    protected $fillable = ['slug', 'name', 'description', 'boundaries_description', 'display_order'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('verification_status', 'publishable')->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
