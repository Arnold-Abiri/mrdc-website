<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContentTranslation extends Model
{
    public const LOCALES = ['sn', 'nd'];

    protected $fillable = ['locale', 'field', 'value'];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
