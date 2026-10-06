<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorEvent extends Model
{
    protected $fillable = ['exception_class', 'summary', 'route', 'correlation_id', 'resolved'];

    protected function casts(): array
    {
        return ['resolved' => 'boolean'];
    }
}
