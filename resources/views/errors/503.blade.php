@php
    $code = 503;
    $title = "Maintenance in progress";
    $message = "The website is temporarily offline for scheduled maintenance. Please check back soon.";
@endphp
@include("errors.layout", ["code" => $code, "title" => $title, "message" => $message, "reference" => null])
