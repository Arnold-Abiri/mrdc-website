<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailConfiguration extends Model
{
    use HasFactory;

    protected $table = 'mail_configurations';

    protected $fillable = [
        'name',
        'provider',
        'host',
        'port',
        'encryption',
        'username',
        'password',
        'from_name',
        'from_address',
        'reply_to_address',
        'is_active',
        'mail_enabled',
        'last_tested_at',
        'last_tested_status',
        'last_tested_error',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'is_active' => 'boolean',
            'mail_enabled' => 'boolean',
            'username' => 'encrypted',
            'password' => 'encrypted',
            'last_tested_at' => 'datetime',
        ];
    }

    public function activate(): void
    {
        static::query()->where('id', '!=', $this->id)->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }

    /**
     * @param  Builder<MailConfiguration>  $query
     * @return Builder<MailConfiguration>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
