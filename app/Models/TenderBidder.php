<?php

namespace App\Models;

use Database\Factories\TenderBidderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderBidder extends Model
{
    /** @use HasFactory<TenderBidderFactory> */
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'company_name',
        'representative_name',
        'email',
        'phone',
        'downloaded_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'downloaded_at' => 'datetime',
        ];
    }

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }
}
