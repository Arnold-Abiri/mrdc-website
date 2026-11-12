<?php

namespace App\Models;

use App\Domain\Content\HasTranslations;
use App\Domain\Content\TranslatableContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model implements TranslatableContent
{
    use HasTranslations;

    public const TRANSLATABLE_FIELDS = ['purpose', 'instructions', 'code_label'];

    protected $fillable = [
        'payment_type',
        'code_label',
        'instructions',
        'bank_name',
        'account_name',
        'account_number',
        'branch_name',
        'branch_code',
        'currency',
        'category',
        'purpose',
        'display_order',
        'is_active',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('status', 'published');
    }
}
