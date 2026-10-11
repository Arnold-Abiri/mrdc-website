<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Intermediate model for a gallery's ordered album images.
 *
 * @property int $gallery_id
 * @property int $media_id
 * @property string|null $caption
 * @property int $display_order
 */
class GalleryMedia extends Pivot
{
    protected $table = 'gallery_media';
}
