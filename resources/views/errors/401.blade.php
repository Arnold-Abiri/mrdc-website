@php
    $code = 401;
    $title = "Sign-in required";
    $message = "This page requires you to sign in first.";
@endphp
@include("errors.layout", ["code" => $code, "title" => $title, "message" => $message, "reference" => null])
