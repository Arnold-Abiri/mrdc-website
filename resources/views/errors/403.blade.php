@php
    $code = 403;
    $title = "Access denied";
    $message = "You do not have permission to view this page.";
@endphp
@include("errors.layout", ["code" => $code, "title" => $title, "message" => $message, "reference" => null])
