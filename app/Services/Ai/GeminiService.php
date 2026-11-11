<?php

namespace App\Services\Ai;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeminiService
{
    protected ?string $apiKey;

    protected string $model;

    protected string $baseUrl;

    public function __construct(?string $apiKey = null, ?string $model = null, ?string $baseUrl = null)
    {
        $this->apiKey = $apiKey ?? Config::get('services.gemini.key');
        $this->model = $model ?? Config::get('services.gemini.model', 'gemini-3.5-flash-lite');
        $this->baseUrl = rtrim($baseUrl ?? Config::get('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
    }

    /**
     * Check if Gemini API is properly configured with an API key.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->apiKey);
    }

    /**
     * Build the standard system instruction for Mutoko Rural District Council.
     * Enforces polite trilingual support (English, Shona, Ndebele) and anti-hallucination rules.
     */
    public function getSystemInstruction(?string $additionalContext = null): string
    {
        $instruction = <<<'PROMPT'
You are the official Artificial Intelligence Citizen Assistant for Mutoko Rural District Council (MRDC) in Zimbabwe.
Your mission is to serve citizens, ratepayers, businesses, and visitors with accurate, courteous, and prompt guidance.

LANGUAGE AND DIALECT RULES:
1. You are fluent in English, ChiShona, and isiNdebele.
2. ALWAYS detect and reply in the primary language used by the citizen.
   - If the inquiry is in ChiShona, reply in fluent, courteous ChiShona.
   - If the inquiry is in isiNdebele, reply in fluent, courteous isiNdebele.
   - If the inquiry is in English, reply in clear, professional English.
   - If the citizen code-switches (e.g. mixes Shona and English), reply in courteous, accessible ChiShona or English.
3. Use respectful Zimbabwean civic honorifics where appropriate (e.g. in Shona: "Makadii", "Ndatenda", "Tingakubatsirai sei nhasi?").

FACTUAL GROUNDING AND ANTI-HALLUCINATION RULES:
1. Answer strictly based on verified council facts, bylaws, fee structures, and procedures provided in your context.
2. If specific fees, rates, account numbers, or bylaws are not provided in the context, do NOT invent them. Explicitly advise the citizen to contact the MRDC Main Offices at Mutoko Growth Point or call/email the council directly.
3. Never issue binding legal commitments or approve applications on behalf of the council.

COUNCIL DETAILS FOR MUTOKO RDC:
- Location: Mutoko Growth Point, Mashonaland East Province, Zimbabwe.
- Primary Functions: Roads, Water & Sanitation, Health Services, Refuse Collection, Housing & Land Allocation, Business Licensing, Natural Resource Governance.
PROMPT;

        if ($additionalContext) {
            $instruction .= "\n\nVERIFIED COUNCIL CONTEXT:\n".$additionalContext;
        }

        return $instruction;
    }

    /**
     * Generate a response for a citizen inquiry with multilingual grounding.
     *
     * @param  array<int, array{role: string, content: string}>  $conversationHistory
     * @return array{success: bool, text?: string, error?: string, message?: string, usage?: array<string, mixed>}
     */
    public function askCitizenAssistant(string $userMessage, array $conversationHistory = [], ?string $additionalContext = null): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'API_KEY_NOT_CONFIGURED',
                'message' => 'Gemini API key is not configured. Please check GEMINI_API_KEY in your environment.',
            ];
        }

        $systemInstruction = $this->getSystemInstruction($additionalContext);

        $contents = [];
        foreach ($conversationHistory as $msg) {
            $role = ($msg['role'] === 'user' || $msg['role'] === 'citizen') ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $msg['content']]],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $systemInstruction]],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.3,
                'maxOutputTokens' => 1024,
            ],
        ];

        try {
            $url = sprintf('%s/models/%s:generateContent', $this->baseUrl, $this->model);

            /** @var Response $response */
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $this->apiKey,
            ])
                ->timeout(20)
                ->retry(2, 500)
                ->post($url, $payload);

            if (! $response->successful()) {
                Log::error('Gemini API Error: '.$response->body(), [
                    'status' => $response->status(),
                ]);

                return [
                    'success' => false,
                    'error' => 'API_CALL_FAILED',
                    'message' => 'Failed to receive a response from Gemini API: '.$response->status(),
                ];
            }

            $json = $response->json();
            $candidateText = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (! $candidateText) {
                return [
                    'success' => false,
                    'error' => 'EMPTY_RESPONSE',
                    'message' => 'Received an empty response from the AI model.',
                ];
            }

            return [
                'success' => true,
                'text' => trim($candidateText),
                'usage' => $json['usageMetadata'] ?? [],
            ];
        } catch (Throwable $e) {
            Log::error('Gemini Service Exception: '.$e->getMessage(), ['exception' => $e]);

            return [
                'success' => false,
                'error' => 'SERVICE_EXCEPTION',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Classify an incoming citizen enquiry or complaint to identify department, urgency, and category.
     * Returns structured JSON data.
     *
     * @param  array<string, mixed>  $enquiryData
     * @return array{success: bool, classification?: array<string, mixed>, error?: string}
     */
    public function classifyEnquiry(array $enquiryData): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'API_KEY_NOT_CONFIGURED'];
        }

        $prompt = <<<PROMPT
You are a public enquiry triage engine for Mutoko Rural District Council.
Analyze the following citizen report or inquiry and categorize it strictly in JSON format.

Citizen Subject: {$enquiryData['subject']}
Citizen Message: {$enquiryData['message']}

Determine:
1. "department": One of ["Engineering & Technical Services", "Health & Environmental Services", "Housing & Community Services", "Finance & Administration", "Governance & Executive", "General"]
2. "priority": One of ["low", "medium", "high", "urgent"] (Urgent if there is immediate health hazard, burst sewer/water main, or danger to life).
3. "language": One of ["en", "sn", "nd"] (Primary language of the message).
4. "sentiment": One of ["positive", "neutral", "frustrated", "urgent"]
5. "summary_en": A one-sentence summary in English.
6. "summary_sn": A one-sentence summary in ChiShona.

Respond ONLY with valid JSON. Do not include markdown code block formatting (e.g. no ```json).
PROMPT;

        try {
            $url = sprintf('%s/models/%s:generateContent', $this->baseUrl, $this->model);

            /** @var Response $response */
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $this->apiKey,
            ])
                ->timeout(15)
                ->post($url, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => $prompt]],
                        ],
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'temperature' => 0.1,
                    ],
                ]);

            if (! $response->successful()) {
                return ['success' => false, 'error' => 'API_CALL_FAILED'];
            }

            $rawText = $response->json('candidates.0.content.parts.0.text', '');
            $cleanJson = trim($rawText);
            $cleanJson = preg_replace('/^```(?:json)?\s*/i', '', $cleanJson);
            $cleanJson = preg_replace('/\s*```$/', '', $cleanJson);

            $data = json_decode($cleanJson, true);

            if (! is_array($data)) {
                return ['success' => false, 'error' => 'INVALID_JSON_RESPONSE'];
            }

            return [
                'success' => true,
                'classification' => $data,
            ];
        } catch (Throwable $e) {
            Log::error('Gemini Classification Exception: '.$e->getMessage());

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
