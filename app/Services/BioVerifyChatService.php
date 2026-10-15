<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Talks to the RAG chat service. Exception messages thrown from here
 * are safe to show to users; technical details go to the log.
 */
class BioVerifyChatService
{
    public function ask(string $message): array
    {
        $baseUrl = rtrim(
            config('services.bioverify_chat.url', 'http://127.0.0.1:8002'),
            '/'
        );

        try {
            $response = Http::connectTimeout(5)
                ->timeout(190)
                ->post($baseUrl . '/chat', [
                    'message' => $message,
                ]);

            if ($response->failed()) {
                $detail = $response->json('detail');

                Log::warning('Pong AI chat request failed', [
                    'status' => $response->status(),
                    'detail' => is_string($detail) ? $detail : json_encode($detail),
                ]);

                throw new RuntimeException(
                    'Pong AI could not answer right now. Please try again in a moment.'
                );
            }

            $data = $response->json();

            if (!is_array($data) || empty($data['answer'])) {
                throw new RuntimeException(
                    'Pong AI returned an empty answer. Please try again.'
                );
            }

            return [
                'answer' => $data['answer'],
                'sources' => $data['sources'] ?? [],
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Pong AI chat connection failed', [
                'error' => $e->getMessage(),
            ]);

            // Covers both "service not running" and "took too long".
            throw new RuntimeException(
                str_contains($e->getMessage(), 'timed out')
                    ? 'Pong AI took too long to answer. Try a shorter question.'
                    : 'Pong AI is offline. Make sure the RAG service is running on port 8002.',
                previous: $e
            );
        }
    }
}
