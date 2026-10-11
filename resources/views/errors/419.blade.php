@php
    $code = 419;
    $title = "Session expired";
    $message = "Your session has expired for security reasons. Please reload the page and try again.";
@endphp
@include("errors.layout", ["code" => $code, "title" => $title, "message" => $message, "reference" => null])
