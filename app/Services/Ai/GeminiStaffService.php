<?php

namespace App\Services\Ai;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeminiStaffService
{
    public function __construct(
        protected GeminiService $geminiService,
        protected ?GroqService $groqService = null
    ) {
        $this->groqService = $this->groqService ?? app(GroqService::class);
    }

    /**
     * Generate Shona or Ndebele translations for an official council notice or news item.
     *
     * @param  string  $title  English title
     * @param  string  $summary  English summary
     * @param  string  $body  English markdown body
     * @param  string|null  $seoTitle  English SEO title
     * @param  string|null  $metaDescription  English Meta description
     * @param  string  $targetLanguage  'sn' for Shona, 'nd' for Ndebele
     * @return array{success: bool, title?: string, summary?: string, body?: string, seo_title?: string, meta_description?: string, error?: string}
     */
    public function translateEditorial(
        string $title,
        string $summary,
        string $body,
        ?string $seoTitle = null,
        ?string $metaDescription = null,
        string $targetLanguage = 'sn'
    ): array {
        if (! $this->geminiService->isConfigured() && ! $this->groqService->isConfigured()) {
            return ['success' => false, 'error' => 'API_KEY_NOT_CONFIGURED'];
        }

        $langName = $targetLanguage === 'nd' ? 'isiNdebele' : 'ChiShona';
        $seoTitleText = $seoTitle ?: $title;
        $metaDescText = $metaDescription ?: ($summary ?: mb_substr(strip_tags($body), 0, 150));

        $prompt = <<<PROMPT
You are the official linguistic translator for Mutoko Rural District Council in Zimbabwe.
Translate the following English council publication into official, clear, and culturally courteous {$langName}.

English Title: {$title}
English Summary: {$summary}
English SEO Title: {$seoTitleText}
English Meta Description: {$metaDescText}
English Body (Markdown):
{$body}

Translate each part accurately. Use standard civic and local government vocabulary suitable for residents of Mutoko.
Respond strictly in JSON format with keys:
{
  "title": "Translated title",
  "summary": "Translated summary",
  "body": "Translated body keeping original markdown formatting",
  "seo_title": "Translated SEO title (under 70 characters)",
  "meta_description": "Translated Meta description (under 160 characters)"
}

Do NOT output markdown code blocks (e.g. no ```json). Output raw JSON only.
PROMPT;

        return $this->callJsonEndpoint($prompt);
    }

    /**
     * Translate arbitrary content fields of an entity into Shona or Ndebele.
     *
     * @param  string  $entityName  Name or model title of the entity (e.g. "Service", "Department", "Tender")
     * @param  array<string, string>  $fieldsWithValues  Associative array of field_name => english_text
     * @param  string  $targetLanguage  'sn' for Shona, 'nd' for Ndebele
     * @return array{success: bool, translations?: array<string, string>, error?: string}
     */
    public function translateFields(string $entityName, array $fieldsWithValues, string $targetLanguage = 'sn'): array
    {
        if (! $this->geminiService->isConfigured() && ! $this->groqService->isConfigured()) {
            return ['success' => false, 'error' => 'API_KEY_NOT_CONFIGURED'];
        }

        $fieldsToTranslate = array_filter($fieldsWithValues, fn ($v): bool => is_string($v) && trim($v) !== '');
        if ($fieldsToTranslate === []) {
            return ['success' => true, 'translations' => []];
        }

        $langName = $targetLanguage === 'nd' ? 'isiNdebele' : 'ChiShona';
        $fieldsJson = json_encode($fieldsToTranslate, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $prompt = <<<PROMPT
You are the official linguistic translator for Mutoko Rural District Council in Zimbabwe.
Translate the following English content fields for a council {$entityName} into official, clear, and culturally courteous {$langName}.
Use standard civic and local government vocabulary suitable for residents of Mutoko.
Preserve markdown formatting, bullet points, line breaks, or structural elements where present.

English Fields:
{$fieldsJson}

Respond strictly in JSON format where the keys are the exact same field names provided above, and the values are their translations in {$langName}:
{
PROMPT;

        foreach (array_keys($fieldsToTranslate) as $idx => $key) {
            $comma = ($idx < count($fieldsToTranslate) - 1) ? ',' : '';
            $prompt .= "\n  \"{$key}\": \"Translated {$key} in {$langName}\"{$comma}";
        }
        $prompt .= "\n}\n\nDo NOT output markdown code blocks (e.g. no ```json). Output raw JSON only.";

        $result = $this->callJsonEndpoint($prompt);
        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $translations = [];
        foreach (array_keys($fieldsToTranslate) as $key) {
            if (isset($result[$key]) && is_string($result[$key])) {
                $translations[$key] = $result[$key];
            }
        }

        return [
            'success' => true,
            'translations' => $translations,
        ];
    }

    /**
     * Translate a single short content field (e.g. a contact office name) into Shona or Ndebele.
     *
     * @return array{success: bool, translation?: string, error?: string}
     */
    public function translateField(string $text, string $fieldLabel, string $targetLanguage = 'sn'): array
    {
        if (! $this->geminiService->isConfigured()) {
            return ['success' => false, 'error' => 'API_KEY_NOT_CONFIGURED'];
        }

        $langName = $targetLanguage === 'nd' ? 'isiNdebele' : 'ChiShona';

        $prompt = <<<PROMPT
You are the official linguistic translator for Mutoko Rural District Council in Zimbabwe.
Translate the following English {$fieldLabel} into official, clear, and culturally courteous {$langName}.
Use standard civic and local government vocabulary suitable for residents of Mutoko.

English text: {$text}

Respond strictly in JSON format with keys:
{
  "translation": "Translated text"
}

Do NOT output markdown code blocks (e.g. no ```json). Output raw JSON only.
PROMPT;

        return $this->callJsonEndpoint($prompt);
    }

    /**
     * Draft an official response to a citizen enquiry in English, Shona, or Ndebele.
     *
     * @param  array{name: string, subject: string, message: string, category: string}  $enquiry
     * @param  string  $officerInstructions  Brief guidance from the council officer
     * @param  string  $language  'en', 'sn', or 'nd'
     * @return array{success: bool, response?: string, error?: string}
     */
    public function draftEnquiryResponse(array $enquiry, string $officerInstructions, string $language = 'en'): array
    {
        if (! $this->geminiService->isConfigured() && ! $this->groqService->isConfigured()) {
            return ['success' => false, 'error' => 'API_KEY_NOT_CONFIGURED'];
        }

        $langInstruction = match ($language) {
            'sn' => 'Respond in polite, respectful ChiShona (e.g. starting with "Kwaziwai [Name]", "Makadii").',
            'nd' => 'Respond in polite, respectful isiNdebele (e.g. starting with "Salibonani [Name]").',
            default => 'Respond in professional, empathetic, and courteous English.',
        };

        $prompt = <<<PROMPT
You are an administrative communication officer for Mutoko Rural District Council.
Draft an official email response from the council to a citizen inquiry.

CITIZEN DETAILS:
- Name: {$enquiry['name']}
- Subject: {$enquiry['subject']}
- Citizen Message: {$enquiry['message']}
- Category: {$enquiry['category']}

OFFICER'S NOTES / ACTION TAKEN:
{$officerInstructions}

LANGUAGE REQUIREMENT:
{$langInstruction}

GUIDELINES:
1. Acknowledge their message warmly with civic courtesy.
2. Incorporate the officer's notes clearly and explain next steps or resolution.
3. Sign off as:
   Mutoko Rural District Council
   Customer Care & Community Liaison Desk
   Mutoko Growth Point, Zimbabwe

Provide ONLY the final message body ready to be sent to the citizen.
PROMPT;

        try {
            $apiKey = Config::get('services.gemini.key');
            $model = Config::get('services.gemini.model', 'gemini-3.5-flash-lite');
            $baseUrl = rtrim(Config::get('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');

            /** @var Response $response */
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $apiKey,
            ])
                ->timeout(20)
                ->post("{$baseUrl}/models/{$model}:generateContent", [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => $prompt]],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.4,
                    ],
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException('Gemini API Error: '.$response->body());
            }

            $text = $response->json('candidates.0.content.parts.0.text', '');

            return [
                'success' => true,
                'response' => trim($text),
            ];
        } catch (Throwable $e) {
            Log::warning('Gemini draft response failed, falling back to Groq Cloud: '.$e->getMessage());

            if ($this->groqService->isConfigured()) {
                $groqRes = $this->groqService->askCitizenAssistant($prompt);
                if ($groqRes['success'] ?? false) {
                    return [
                        'success' => true,
                        'response' => $groqRes['text'] ?? '',
                    ];
                }
            }

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Helper to invoke Gemini with JSON response schema.
     *
     * @return array{success: bool, error?: string}
     */
    protected function callJsonEndpoint(string $prompt): array
    {
        try {
            $apiKey = Config::get('services.gemini.key');
            $model = Config::get('services.gemini.model', 'gemini-3.5-flash-lite');
            $baseUrl = rtrim(Config::get('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');

            /** @var Response $response */
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $apiKey,
            ])
                ->timeout(45)
                ->retry(3, 1000, throw: false)
                ->post("{$baseUrl}/models/{$model}:generateContent", [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => $prompt]],
                        ],
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'temperature' => 0.2,
                    ],
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException('Gemini API Error: '.$response->body());
            }

            $rawText = $response->json('candidates.0.content.parts.0.text', '');
            $cleanJson = trim($rawText);
            $cleanJson = preg_replace('/^```(?:json)?\s*/i', '', $cleanJson);
            $cleanJson = preg_replace('/\s*```$/', '', $cleanJson);

            $data = json_decode($cleanJson, true);

            if (! is_array($data)) {
                throw new \RuntimeException('INVALID_JSON_RESPONSE');
            }

            return array_merge(['success' => true], $data);
        } catch (Throwable $e) {
            Log::warning('Gemini JSON Endpoint failed, falling back to Groq Cloud: '.$e->getMessage());

            if ($this->groqService->isConfigured()) {
                $groqResult = $this->groqService->callJsonEndpoint($prompt);
                if ($groqResult['success'] ?? false) {
                    return $groqResult;
                }
            }

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
