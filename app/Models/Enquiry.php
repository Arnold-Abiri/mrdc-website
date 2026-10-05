<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'category', 'department_id', 'subject', 'message'];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime'];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** @return HasMany<EnquiryNote, $this> */
    public function notes(): HasMany
    {
        return $this->hasMany(EnquiryNote::class);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('submitted_at');
    }
}
