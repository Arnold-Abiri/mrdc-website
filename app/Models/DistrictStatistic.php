<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DistrictStatistic extends Model
{
    protected $fillable = ['label', 'value', 'unit', 'icon', 'source_note', 'display_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
