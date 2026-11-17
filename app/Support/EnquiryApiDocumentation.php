<?php

namespace App\Support;

use App\Domain\Cms\EnquiryManager;

class EnquiryApiDocumentation
{
    /** @return array<string, mixed> */
    public static function paths(): array
    {
        $paths = [];
        $trackingProperties = [
            'reference' => ['type' => 'string', 'format' => 'uuid'],
            'status' => ['type' => 'string', 'enum' => ['new', 'in_progress', 'resolved', 'closed']],
            'submitted_at' => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
            'updated_at' => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
        ];
        foreach (['enquiries' => 'Enquiries', 'feedback' => 'Feedback'] as $path => $tag) {
            $properties = [
                'name' => ['type' => 'string', 'maxLength' => 160],
                'email' => ['type' => 'string', 'format' => 'email', 'maxLength' => 254],
                'phone' => ['type' => 'string', 'maxLength' => 40, 'nullable' => true],
                'organisation' => ['type' => 'string', 'maxLength' => 255, 'nullable' => true],
                'category' => ['type' => 'string', 'enum' => EnquiryManager::CITIZEN_CATEGORIES, 'default' => $path === 'feedback' ? 'feedback' : 'general'],
                'subject' => ['type' => 'string', 'maxLength' => 200],
                'message' => ['type' => 'string', 'minLength' => 10, 'maxLength' => 5000],
                'department_id' => ['type' => 'integer', 'nullable' => true, 'description' => 'An active department ID.'],
                'context_type' => ['type' => 'string', 'enum' => EnquiryManager::CONTEXT_TYPES, 'nullable' => true],
                'context_reference' => ['type' => 'string', 'maxLength' => 160, 'nullable' => true, 'description' => 'Published context slug; invalid contexts are discarded.'],
                'consent_given' => ['type' => 'boolean'],
                'website' => ['type' => 'string', 'maxLength' => 0, 'description' => 'Honeypot; omit or leave empty.'],
            ];
            $errors = [
                '422' => ['description' => 'Validation failed', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ValidationError']]]],
                '429' => ['description' => 'Rate limit exceeded.'],
            ];
            $paths['/'.$path] = ['post' => [
                'tags' => [$tag], 'operationId' => 'submit'.$tag,
                'summary' => 'Submit '.$path,
                'description' => 'Limited to 5 submissions per minute per client across feedback and enquiries. Save the tracking token; it is returned only once. Staff-only fields are ignored.',
                'requestBody' => self::body(['type' => 'object', 'required' => ['name', 'email', 'subject', 'message'], 'properties' => $properties, 'example' => ['name' => 'Resident', 'email' => 'resident@example.com', 'subject' => 'Water supply question', 'message' => 'Please advise when water supply will resume.']]),
                'responses' => ['201' => self::response('Submission received', $trackingProperties + ['tracking_token' => ['type' => 'string', 'minLength' => 64, 'maxLength' => 64]])] + $errors,
            ]];
            $paths['/'.$path.'/track'] = ['post' => [
                'tags' => [$tag], 'operationId' => 'track'.$tag,
                'summary' => 'Track a submission',
                'description' => 'Requires the full reference and private token returned by either submission endpoint. Limited to 10 tracking requests per minute per client. Returns no personal details or staff notes. Existing website submissions have no token and cannot be tracked here.',
                'requestBody' => self::body(['type' => 'object', 'required' => ['reference', 'tracking_token'], 'properties' => [
                    'reference' => ['type' => 'string', 'format' => 'uuid'],
                    'tracking_token' => ['type' => 'string', 'pattern' => '^[a-f0-9]{64}$', 'writeOnly' => true],
                ]]),
                'responses' => ['200' => self::response('Current submission progress', $trackingProperties), '404' => ['description' => 'Reference or token is incorrect, or tracking is unavailable.']] + $errors,
            ]];
        }

        return $paths;
    }

    /** @param array<string, mixed> $schema @return array<string, mixed> */
    private static function body(array $schema): array
    {
        return ['required' => true, 'content' => ['application/json' => ['schema' => $schema]]];
    }

    /** @param array<string, mixed> $properties @return array<string, mixed> */
    private static function response(string $description, array $properties): array
    {
        return ['description' => $description, 'content' => ['application/json' => ['schema' => [
            'type' => 'object', 'required' => ['data'], 'properties' => ['data' => [
                'type' => 'object', 'required' => array_keys($properties), 'properties' => $properties, 'additionalProperties' => false,
            ]],
        ]]]];
    }
}
