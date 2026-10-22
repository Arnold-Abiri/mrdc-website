<?php

use App\Models\Media;

if (! function_exists('public_route')) {
    /**
     * Generate a URL for a locale-prefixed public route in the current locale.
     */
    function public_route(string $name, mixed $params = []): string
    {
        if (! is_array($params)) {
            $params = [$params];
        }

        $url = route($name, ['locale' => app()->getLocale(), ...$params], false);

        if ($name === 'managed-media.show') {
            $mediaId = $params['media'] ?? reset($params);
            $updatedAt = Media::query()->whereKey($mediaId)->value('updated_at');

            if ($updatedAt !== null) {
                $url .= '?v='.strtotime((string) $updatedAt);
            }
        }

        return $url;
    }
}
