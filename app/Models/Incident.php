<?php

namespace App\Models;

use App\Domain\Content\HasTranslations;
use App\Domain\Content\TranslatableContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Incident extends Model implements TranslatableContent
{
    use HasTranslations;

    public const TRANSLATABLE_FIELDS = ['summary'];

    protected $fillable = ['started_at', 'recovered_at', 'source', 'status', 'summary'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'recovered_at' => 'datetime'];
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function durationMinutes(): ?int
    {
        $attributes = $this->getAttributes();
        if (empty($attributes['started_at']) || empty($attributes['recovered_at'])) {
            return null;
        }

        return (int) Carbon::parse($attributes['started_at'])->diffInMinutes(Carbon::parse($attributes['recovered_at']));
    }
}
