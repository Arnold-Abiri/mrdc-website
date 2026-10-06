<?php

if (! function_exists('public_route')) {
    /**
     * Generate a URL for a locale-prefixed public route in the current locale.
     */
    function public_route(string $name, mixed $params = []): string
    {
        if (! is_array($params)) {
            $params = [$params];
        }

        return route($name, ['locale' => app()->getLocale(), ...$params]);
    }
}
