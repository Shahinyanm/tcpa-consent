<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\ConsentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(private readonly ConsentService $consentService) {}

    public function handleSmsResponse(Request $request): Response
    {
        try {
            $this->consentService->handleIncomingSms(
                $request->input('From'),
                $request->input('Body')
            );
            return response('OK', 200);
        } catch (\Throwable $e) {
            Log::error('Failed to process SMS: ' . $e->getMessage());
            return response('Failed to process SMS', 500);
        }
    }
}
