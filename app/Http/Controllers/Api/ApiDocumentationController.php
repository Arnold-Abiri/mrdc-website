<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\EnquiryApiDocumentation;
use App\Support\PublicApiCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class ApiDocumentationController extends Controller
{
    public function index(): View
    {
        return view('api-documentation');
    }

    public function specification(): JsonResponse
    {
        $paths = [];
        $schemas = [];
        foreach (PublicApiCatalog::all() as $resource => $definition) {
            $schemaName = str_replace(' ', '', $definition['tag']);
            $properties = [];
            foreach ($definition['fields'] as $field) {
                $properties[$field] = PublicApiCatalog::schema($field, $resource);
            }
            if ($resource === 'galleries') {
                $properties['images'] = ['type' => 'array', 'items' => ['type' => 'object', 'properties' => [
                    'url' => ['type' => 'string', 'format' => 'uri'],
                    'alt' => ['type' => 'string', 'nullable' => true],
                    'caption' => ['type' => 'string', 'nullable' => true],
                ]]];
            }
            $schemas[$schemaName] = ['type' => 'object', 'required' => array_keys($properties), 'properties' => $properties, 'additionalProperties' => false];
            $item = ['$ref' => '#/components/schemas/'.$schemaName];
            $locale = ['name' => 'locale', 'in' => 'query', 'description' => 'Response language; missing translations fall back to English.', 'schema' => ['type' => 'string', 'enum' => ['en', 'sn', 'nd'], 'default' => 'en']];
            $errors = [
                '422' => ['description' => 'Invalid query parameters', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ValidationError']]]],
                '429' => ['description' => 'Rate limit exceeded (60 requests per minute per client).'],
            ];
            $paths['/'.$resource] = ['get' => [
                'tags' => [$definition['tag']], 'operationId' => 'list'.$schemaName,
                'summary' => 'List published '.$definition['tag'],
                'parameters' => [$locale,
                    ['name' => 'page', 'in' => 'query', 'schema' => ['type' => 'integer', 'minimum' => 1, 'default' => 1]],
                    ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 15]],
                ],
                'responses' => ['200' => ['description' => 'Paginated public records', 'content' => ['application/json' => ['schema' => [
                    'type' => 'object', 'required' => ['data', 'links', 'meta'], 'properties' => [
                        'data' => ['type' => 'array', 'items' => $item],
                        'links' => ['$ref' => '#/components/schemas/PaginationLinks'],
                        'meta' => ['$ref' => '#/components/schemas/PaginationMeta'],
                    ],
                ]]]]] + $errors,
            ]];
            $paths['/'.$resource.'/{identifier}'] = ['get' => [
                'tags' => [$definition['tag']], 'operationId' => 'get'.$schemaName,
                'summary' => 'Get a published record by '.$definition['key'],
                'parameters' => [$locale, ['name' => 'identifier', 'in' => 'path', 'required' => true,
                    'description' => 'Record '.$definition['key'],
                    'schema' => $definition['key'] === 'id' ? ['type' => 'integer', 'minimum' => 1] : ['type' => 'string'],
                ]],
                'responses' => [
                    '200' => ['description' => 'Public record', 'content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['data'], 'properties' => ['data' => $item]]]]],
                    '404' => ['description' => 'Record missing or not public.'],
                ] + $errors,
            ]];
        }
        $schemas['PaginationLinks'] = ['type' => 'object', 'properties' => array_fill_keys(['first', 'last', 'prev', 'next'], ['type' => 'string', 'nullable' => true])];
        $schemas['PaginationMeta'] = ['type' => 'object', 'properties' => [
            ...array_fill_keys(['current_page', 'last_page', 'per_page', 'total'], ['type' => 'integer']),
            ...array_fill_keys(['from', 'to'], ['type' => 'integer', 'nullable' => true]),
            'path' => ['type' => 'string'],
            'links' => ['type' => 'array', 'items' => ['type' => 'object', 'properties' => [
                'url' => ['type' => 'string', 'nullable' => true], 'label' => ['type' => 'string'], 'active' => ['type' => 'boolean'],
            ]]],
        ]];
        $schemas['ValidationError'] = ['type' => 'object', 'properties' => [
            'message' => ['type' => 'string'],
            'errors' => ['type' => 'object', 'additionalProperties' => ['type' => 'array', 'items' => ['type' => 'string']]],
        ]];

        return response()->json([
            'openapi' => '3.0.3',
            'info' => ['title' => 'Mutoko RDC Public API', 'version' => '1.0.0', 'description' => 'Public council content and resident submissions. Tracking requires a private token. Dates use ISO 8601.'],
            'servers' => [['url' => url('/api/v1')]],
            'tags' => [...array_values(array_map(fn (array $definition): array => ['name' => $definition['tag']], PublicApiCatalog::all())), ['name' => 'Enquiries'], ['name' => 'Feedback']],
            'paths' => $paths + EnquiryApiDocumentation::paths(),
            'components' => ['schemas' => $schemas],
        ]);
    }
}
