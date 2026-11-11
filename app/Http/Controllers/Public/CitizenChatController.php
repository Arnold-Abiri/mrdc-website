<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Ai\CitizenKnowledgeService;
use App\Services\Ai\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CitizenChatController extends Controller
{
    public function __invoke(
        Request $request,
        GeminiService $geminiService,
        CitizenKnowledgeService $knowledgeService
    ): JsonResponse {
        $validator = Validator::make($request->all(), [
            'message' => ['required', 'string', 'min:2', 'max:1000'],
            'history' => ['nullable', 'array', 'max:10'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,model,citizen,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:2000'],
            'locale' => ['nullable', 'string', 'in:en,sn,nd'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'VALIDATION_ERROR',
                'errors' => $validator->errors(),
            ], 422);
        }

        $message = trim((string) $request->input('message'));
        $history = $request->input('history', []);
        $locale = $request->input('locale', app()->getLocale());

        if (! $geminiService->isConfigured()) {
            $offlineMessage = match ($locale) {
                'sn' => 'Desk redu reRubatsiro harina kugadziriswa zvizere parizvino. Tapota batai mahofisi eKanzuru panhare dzinoti +263 77 000 0000 kana email info@mutokordc.gov.zw.',
                'nd' => 'Ihhovisi lethu loncedo alikamiswanga okwamanje. Sicela uthintane lomkhandlu ku +263 77 000 0000 loba ku info@mutokordc.gov.zw.',
                default => 'The council digital help desk is temporarily unavailable. Please contact the council directly at +263 77 000 0000 or email info@mutokordc.gov.zw.',
            };

            return response()->json([
                'success' => false,
                'error' => 'API_NOT_CONFIGURED',
                'reply' => $offlineMessage,
            ], 503);
        }

        $context = $knowledgeService->getCouncilContext();

        $result = $geminiService->askCitizenAssistant($message, $history, $context);

        if (! ($result['success'] ?? false)) {
            $fallbackMessage = match ($locale) {
                'sn' => 'Tine urombo, hatikwanisi kupindura panguva ino. Tapota edzai zvakare kana kubata mahofisi eKanzuru kuMutoko Growth Point.',
                'nd' => 'Siyaxolisa, asenelisi ukuphendula ngalesi sikhathi. Sicela uzame futhi loba ubize ihhovisi lomkhandlu.',
                default => 'We apologize, but we could not complete your request at this moment. Please try again shortly or contact the council offices directly.',
            };

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'ASSISTANT_FAILED',
                'reply' => $fallbackMessage,
            ], 502);
        }

        return response()->json([
            'success' => true,
            'reply' => $result['text'] ?? '',
        ]);
    }
}
