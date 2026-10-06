<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacancy extends Model
{
    protected $fillable = ['slug', 'title', 'grade', 'description', 'responsibilities', 'requirements', 'opens_at', 'closes_at', 'application_instructions', 'document_id', 'department_id', 'display_order'];

    protected function casts(): array
    {
        return ['opens_at' => 'date', 'closes_at' => 'date', 'published_at' => 'datetime', 'verified_at' => 'datetime'];
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
        if ($this->opens_at !== null && today()->lt($this->opens_at)) {
            return false;
        }
        if ($this->closes_at !== null && today()->gt($this->closes_at)) {
            return false;
        }

        return true;
    }
}
