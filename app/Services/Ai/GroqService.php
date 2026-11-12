<?php

namespace App\Services\Ai;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GroqService
{
    protected ?string $apiKey;

    protected string $model;

    protected string $baseUrl;

    public function __construct(?string $apiKey = null, ?string $model = null, ?string $baseUrl = null)
    {
        $this->apiKey = $apiKey ?? Config::get('services.groq.key');
        $this->model = $model ?? Config::get('services.groq.model', 'llama-3.3-70b-versatile');
        $this->baseUrl = rtrim($baseUrl ?? Config::get('services.groq.base_url', 'https://api.groq.com/openai/v1'), '/');
    }

    /**
     * Check if Groq Cloud API key is configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->apiKey);
    }

    /**
     * Build the standard system instruction for Mutoko Rural District Council.
     * Enforces polite trilingual support (English, Shona, Ndebele) and zero AI tell-tales.
     */
    public function getSystemInstruction(?string $additionalContext = null): string
    {
        $instruction = <<<'PROMPT'
You are the official Council Help Desk officer for Mutoko Rural District Council (MRDC) in Zimbabwe.
Your role is to assist citizens, ratepayers, businesses, and visitors with clear, warm, direct, and practical council guidance.

STRICT HUMAN PERSONA & VOICE (ZERO AI TELL-TALES):
1. You are a real, courteous council help desk representative stationed at the Mutoko Growth Point civic offices.
2. NEVER mention or acknowledge that you are an AI, language model, robot, chatbot, automated system, algorithm, or virtual assistant, under any circumstance — even if directly asked "Are you an AI?", "Are you a bot?", or "Who programmed you?".
3. If directly asked about your identity or whether you are an AI/bot, reply strictly as a council officer:
   - English: "This is the Mutoko Rural District Council Help Desk. I am here to assist you with council services, rates, licences, and community enquiries. How may I help you today?"
   - ChiShona: "Iri idesk reKanzuru yeMutoko Rural District Council rekubatsira vagari. Ndiri pano kuti ndikubatsirei maererano nemabasa ekanzuru, mitero, marezinesi, nezvimwe zvinodiwa. Tingakubatsirai nei nhasi?"
   - isiNdebele: "Leli lihhovisi loncedo loMkhandlu weMutoko Rural District Council. Ngingalisiza ngani mayelana lemisebenzi yomkhandlu, imithero, lezinye izindaba?"
4. BANNED AI PHRASES & CLICHÉS:
   - NEVER use meta-announcements: "Certainly!", "I'd be happy to help with that!", "I understand your frustration", "As an AI...", "As mentioned earlier...", "I hope this information finds you well", "Feel free to reach out anytime!", "In conclusion...", "Let's dive into...".
   - NEVER use robotic preambles or wrap-ups. Answer directly, concisely, and with genuine local warmth.
5. IF AN INQUIRY IS OFF-TOPIC (not related to Mutoko RDC, local government, civic services, or local regulations):
   - Do NOT say "As an AI, I can only help with...".
   - Respond like an office clerk: "The council help desk assists with Mutoko RDC services, rates, licensing, planning, and community civic matters. For enquiries outside council jurisdiction, kindly consult the relevant ministry or service provider."

LANGUAGE AND LOCAL TONE RULES:
1. You are fluent in English, ChiShona, and isiNdebele.
2. ALWAYS match the language and dialect of the citizen:
   - ChiShona: authentic, respectful ChiShona with natural local greetings and honorifics ("Kwaziwai", "Makadii henyu", "Maita basa").
   - isiNdebele: authentic, polite isiNdebele ("Salibonani", "Singalisiza ngani?").
   - English: clear, direct, professional Zimbabwean public administration English.
   - Mixed / code-switching: reply naturally in courteous ChiShona or English.

FACTUAL GROUNDING AND ACCURACY:
1. Answer strictly based on verified council facts, bylaws, services, contacts, and banking details provided in your context.
2. If exact individual balances, ward-specific details, or current tender closing dates are not in your context, do not guess or invent them. Directly provide the official council phone, revenue desk, or department contact so the resident gets their exact matter handled.
3. You provide guidance and information; official stamped receipts and legal permits must be obtained at the council offices at Stand 366 Mutoko Centre.

COUNCIL LOCATION & CONTACTS:
- Head Office: Stand 366 Mutoko Centre, Mutoko Growth Point, Mashonaland East Province, Zimbabwe.
- Primary Directorates: Administration & Human Resources, Finance, Engineering & Technical Services, Health & Environmental Services, Housing & Community Services.
PROMPT;

        if ($additionalContext) {
            $instruction .= "\n\nVERIFIED COUNCIL CONTEXT:\n".$additionalContext;
        }

        return $instruction;
    }

    /**
     * Ask citizen assistant using Groq Cloud OpenAI-compatible endpoint.
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
                'message' => 'Groq API key is not configured. Please check GROQ_API_KEY in your environment.',
            ];
        }

        $systemInstruction = $this->getSystemInstruction($additionalContext);

        $messages = [
            ['role' => 'system', 'content' => $systemInstruction],
        ];

        foreach ($conversationHistory as $msg) {
            $text = trim((string) ($msg['content'] ?? ''));
            if ($text === '') {
                continue;
            }

            $role = ($msg['role'] === 'user' || $msg['role'] === 'citizen') ? 'user' : 'assistant';
            $messages[] = [
                'role' => $role,
                'content' => $text,
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.3,
            'max_tokens' => 1024,
        ];

        try {
            $url = sprintf('%s/chat/completions', $this->baseUrl);

            /** @var Response $response */
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$this->apiKey,
            ])
                ->withOptions([
                    'force_ip_resolve' => 'v4',
                ])
                ->connectTimeout(10)
                ->timeout(25)
                ->retry(2, 500, throw: false)
                ->post($url, $payload);

            if (! $response->successful()) {
                Log::error('Groq API Error: '.$response->body(), [
                    'status' => $response->status(),
                ]);

                return [
                    'success' => false,
                    'error' => 'API_CALL_FAILED',
                    'message' => 'Failed to receive a response from Groq API: '.$response->status(),
                ];
            }

            $json = $response->json();
            $candidateText = $json['choices'][0]['message']['content'] ?? null;

            if (! $candidateText) {
                return [
                    'success' => false,
                    'error' => 'EMPTY_RESPONSE',
                    'message' => 'Received an empty response from Groq model.',
                ];
            }

            return [
                'success' => true,
                'text' => trim($candidateText),
                'usage' => $json['usage'] ?? [],
            ];
        } catch (Throwable $e) {
            Log::error('Groq Service Exception: '.$e->getMessage(), ['exception' => $e]);

            return [
                'success' => false,
                'error' => 'SERVICE_EXCEPTION',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Call Groq for structured JSON completions (used for translation & triage fallbacks).
     *
     * @return array{success: bool, error?: string}
     */
    public function callJsonEndpoint(string $prompt, ?string $systemPrompt = null): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'API_KEY_NOT_CONFIGURED'];
        }

        $messages = [];
        if ($systemPrompt) {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        try {
            $url = sprintf('%s/chat/completions', $this->baseUrl);

            /** @var Response $response */
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$this->apiKey,
            ])
                ->withOptions([
                    'force_ip_resolve' => 'v4',
                ])
                ->timeout(30)
                ->retry(2, 500, throw: false)
                ->post($url, [
                    'model' => $this->model,
                    'messages' => $messages,
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.1,
                ]);

            if (! $response->successful()) {
                Log::error('Groq JSON API Error: '.$response->body(), [
                    'status' => $response->status(),
                ]);

                return ['success' => false, 'error' => 'API_CALL_FAILED'];
            }

            $rawText = $response->json('choices.0.message.content', '');
            $cleanJson = trim($rawText);
            $cleanJson = preg_replace('/^```(?:json)?\s*/i', '', $cleanJson);
            $cleanJson = preg_replace('/\s*```$/', '', $cleanJson);

            $data = json_decode($cleanJson, true);

            if (! is_array($data)) {
                return ['success' => false, 'error' => 'INVALID_JSON_RESPONSE'];
            }

            return array_merge(['success' => true], $data);
        } catch (Throwable $e) {
            Log::error('Groq JSON Exception: '.$e->getMessage());

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
