<?php

namespace App\Models;

use App\Domain\Content\HasTranslations;
use App\Domain\Content\TranslatableContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string|null $alt_text
 * @property string|null $caption
 * @property int|null $size
 * @property string $mime_type
 * @property string $storage_path
 * @property string $original_filename
 * @property string $status
 * @property Carbon|null $updated_at
 * @property GalleryMedia $pivot
 */
class Media extends Model implements TranslatableContent
{
    use HasTranslations;

    public const TRANSLATABLE_FIELDS = ['title', 'alt_text', 'caption'];

    protected $table = 'media';

    protected $fillable = ['title', 'alt_text', 'caption'];

    /** @return BelongsTo<Department, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
