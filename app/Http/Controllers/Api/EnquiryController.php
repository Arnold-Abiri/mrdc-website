<?php

namespace App\Http\Controllers\Api;

use App\Domain\Cms\EnquiryManager;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function store(Request $request, EnquiryManager $manager): JsonResponse
    {
        $input = $request->all();
        $input['category'] ??= $request->route('submission_type') === 'feedback' ? 'feedback' : 'general';
        $token = bin2hex(random_bytes(32));
        $enquiry = $manager->submit($input, hash('sha256', $token));

        return response()->json(['data' => [
            ...$this->trackingData($enquiry),
            'tracking_token' => $token,
        ]], 201)->header('Cache-Control', 'no-store');
    }

    public function track(Request $request): JsonResponse
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'uuid'],
            'tracking_token' => ['required', 'string', 'size:64', 'regex:/\A[a-f0-9]{64}\z/'],
        ]);
        $enquiry = Enquiry::query()->where('public_id', $data['reference'])->first();
        $expected = $enquiry?->tracking_token_hash ?? str_repeat('0', 64);
        $matches = hash_equals($expected, hash('sha256', $data['tracking_token']));
        abort_unless($enquiry !== null && $enquiry->tracking_token_hash !== null && $matches, 404, 'Submission not found.');

        return response()->json(['data' => $this->trackingData($enquiry)])->header('Cache-Control', 'no-store');
    }

    /** @return array<string, mixed> */
    private function trackingData(Enquiry $enquiry): array
    {
        return [
            'reference' => $enquiry->public_id,
            'status' => $enquiry->status,
            'submitted_at' => $enquiry->submitted_at?->toISOString(),
            'updated_at' => $enquiry->updated_at?->toISOString(),
        ];
    }
}
