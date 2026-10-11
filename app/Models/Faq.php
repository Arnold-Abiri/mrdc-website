<?php

namespace App\Models;

use App\Domain\Content\HasTranslations;
use App\Domain\Content\TranslatableContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model implements TranslatableContent
{
    use HasTranslations;

    public const TRANSLATABLE_FIELDS = ['question', 'answer'];

    public const CATEGORIES = [
        'billing' => 'Rates & Billing',
        'licences' => 'Business & Licences',
        'land_housing' => 'Housing & Land',
        'governance' => 'Council & Governance',
        'health_water' => 'Health & Environment',
        'general' => 'General Enquiries',
    ];

    protected $fillable = [
        'question',
        'answer',
        'category',
        'department_id',
        'display_order',
        'is_published',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_published', true)->where('is_featured', true);
    }
}
