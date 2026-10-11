@php
    $code = 500;
    $title = "Service unavailable";
    $message = "Something went wrong on our side. Our team has been notified. Please try again shortly.";
@endphp
@include("errors.layout", ["code" => $code, "title" => $title, "message" => $message, "reference" => null])
