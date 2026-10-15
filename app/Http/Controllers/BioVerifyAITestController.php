<?php

namespace App\Http\Controllers;

use App\Services\BioVerifyAIService;
use Illuminate\Http\JsonResponse;

class BioVerifyAITestController extends Controller
{
    public function health(
        BioVerifyAIService $aiService
    ): JsonResponse {
        try {
            $result = $aiService->health();

            return response()->json([
                'success' => true,
                'laravel' => 'online',
                'python_ai' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'laravel' => 'online',
                'python_ai' => 'offline',
                'message' => $e->getMessage(),
            ], 503);
        }
    }
}
