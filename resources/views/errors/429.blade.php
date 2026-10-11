@php
    $code = 429;
    $title = "Too many requests";
    $message = "You have made too many requests in a short time. Please wait a moment and try again.";
@endphp
@include("errors.layout", ["code" => $code, "title" => $title, "message" => $message, "reference" => null])
