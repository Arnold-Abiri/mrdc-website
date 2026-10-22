<?php

$publicPath = realpath(__DIR__.'/../../public');
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$requestedFile = is_string($requestPath) ? realpath($publicPath.$requestPath) : false;

if ($requestedFile !== false && str_starts_with($requestedFile, $publicPath.DIRECTORY_SEPARATOR) && is_file($requestedFile)) {
    return false;
}

require $publicPath.'/index.php';
