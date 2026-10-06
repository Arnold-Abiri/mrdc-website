<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['event_type', 'route', 'locale', 'subject_type', 'subject', 'visitor_key', 'referrer_category', 'referrer_domain', 'created_at'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }
}
