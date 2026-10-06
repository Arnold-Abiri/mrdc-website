<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouncilMeeting extends Model
{
    protected $fillable = ['title', 'meeting_type', 'scheduled_date', 'scheduled_time', 'venue', 'meeting_status', 'summary', 'agenda_document_id', 'minutes_document_id', 'department_id', 'display_order'];

    protected function casts(): array
    {
        return ['scheduled_date' => 'date', 'published_at' => 'datetime', 'verified_at' => 'datetime'];
    }

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'agenda_document_id');
    }

    public function minutes(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'minutes_document_id');
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
