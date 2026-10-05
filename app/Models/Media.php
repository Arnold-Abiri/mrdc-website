<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = ['title', 'alt_text', 'caption'];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
