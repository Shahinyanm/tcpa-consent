<?php

namespace App\Http\Controllers\Api;


use App\Exceptions\InvalidVerificationCodeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateConsentRequest;
use App\Http\Requests\VerifyCodeRequest;
use App\Http\Resources\ConsentResource;
use App\Models\Consent;
use App\Services\ConsentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ConsentController extends Controller
{
    public function __construct(private readonly ConsentService $consentService) {}

    public function store(CreateConsentRequest $request): JsonResponse
    {
//        try {
            $consent = $this->consentService->createConsent($request->getConsentData());
            return (new ConsentResource($consent))->response()->setStatusCode(201);
//        } catch (\Throwable $e) {
//            Log::error('Failed to create consent: ' . $e->getMessage());
//            return response()->json(['error' => 'Failed to create consent'], 500);
//        }
    }

    public function verifyCode(VerifyCodeRequest $request, Consent $consent): JsonResponse
    {
        try {
            $result = $this->consentService->verifyCode($consent, $request->code);
            return response()->json(['success' => $result]);
        } catch (InvalidVerificationCodeException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            Log::error('Failed to verify code: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to verify code'], 500);
        }
    }
}
