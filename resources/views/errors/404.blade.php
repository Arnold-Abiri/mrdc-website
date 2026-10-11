@php
    $code = 404;
    $title = "Page not found";
    $message = "The page you are looking for does not exist or was moved.";
@endphp
@include("errors.layout", ["code" => $code, "title" => $title, "message" => $message, "reference" => null])
