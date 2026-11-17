<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mutoko RDC API Documentation</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui.css">
</head>
<body>
    <div id="swagger-ui"></div>
    <noscript>Enable JavaScript to use Swagger UI. <a href="{{ route('api.openapi') }}">Download the OpenAPI specification</a>.</noscript>
    <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-bundle.js" crossorigin="anonymous"></script>
    <script>
        window.onload = () => {
            SwaggerUIBundle({
                url: @json(route('api.openapi')),
                dom_id: '#swagger-ui',
                deepLinking: true,
                validatorUrl: null,
                supportedSubmitMethods: ['get', 'post'],
            });
        };
    </script>
</body>
</html>
