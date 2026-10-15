<?php

namespace App\Http\Controllers;

use App\Services\BioVerifyChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class BioVerifyChatController extends Controller
{
    public function index(): View
    {
        return view('ai-chat');
    }

    public function send(
        Request $request,
        BioVerifyChatService $chatService
    ): JsonResponse {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:1', 'max:4000'],
        ]);

        // The chat service allows 190s; keep PHP's limit above that.
        set_time_limit(220);

        try {
            $result = $chatService->ask(trim($validated['message']));

            return response()->json([
                'success' => true,
                'answer' => $result['answer'],
                'sources' => $result['sources'],
            ]);
        } catch (RuntimeException $e) {
            // BioVerifyChatService messages are written for users.
            report($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 503);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Pong AI is unavailable right now. Please try again.',
            ], 503);
        }
    }
}
