<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string|null $alt_text
 * @property string|null $caption
 */
class Media extends Model
{
    protected $table = 'media';

    protected $fillable = ['title', 'alt_text', 'caption'];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
