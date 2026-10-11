@php
    $code = 400;
    $title = "Bad request";
    $message = "The request could not be understood. Please check the address and try again.";
@endphp
@include("errors.layout", ["code" => $code, "title" => $title, "message" => $message, "reference" => null])
