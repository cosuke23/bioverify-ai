<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BioVerifyAIService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            (string) config('services.bioverify_ai.url'),
            '/'
        );

        if ($this->baseUrl === '') {
            throw new RuntimeException(
                'BioVerify AI URL is not configured in services.php.'
            );
        }
    }

    /**
     * Check whether the BioVerify AI service is available.
     */
    public function health(): array
    {
        return $this->sendRequest(
            fn() => Http::timeout(5)
                ->get($this->baseUrl . '/health'),
            'BioVerify AI service is unavailable.'
        );
    }

    /**
     * Recognize a face using registered face embeddings.
     */
    public function recognizeFace(
        UploadedFile $image,
        array $registeredFaces
    ): array {
        $registeredFacesJson = $this->encodePayload(
            $registeredFaces,
            'registered face embeddings'
        );

        Log::info('BioVerify registered faces payload', [
            'count' => count($registeredFaces),
            'json_length' => strlen($registeredFacesJson),
        ]);

        $contents = $this->readFile($image);

        return $this->sendRequest(
            fn() => Http::timeout(60)
                ->attach(
                    'face_image',
                    $contents,
                    $image->getClientOriginalName()
                )
                ->post(
                    $this->baseUrl . '/face/recognize',
                    [
                        'registered_faces' => $registeredFacesJson,
                    ]
                ),
            'The AI service could not recognize the face.'
        );
    }

    /**
     * Generate an embedding from a voice recording.
     */
    public function generateVoiceEmbedding(
        UploadedFile $audio
    ): array {
        $contents = $this->readFile($audio);

        return $this->sendRequest(
            fn() => Http::timeout(120)
                ->attach(
                    'voice_audio',
                    $contents,
                    $audio->getClientOriginalName()
                )
                ->post(
                    $this->baseUrl . '/voice/embedding'
                ),
            'The AI service could not generate the voice embedding.'
        );
    }

    /**
     * Recognize a voice against registered voice embeddings.
     */
    public function recognizeVoice(
        UploadedFile $audio,
        array $registeredVoices
    ): array {
        $registeredVoicesJson = $this->encodePayload(
            $registeredVoices,
            'registered voice embeddings'
        );

        Log::info('BioVerify registered voices payload', [
            'count' => count($registeredVoices),
            'json_length' => strlen($registeredVoicesJson),
        ]);

        $contents = $this->readFile($audio);

        return $this->sendRequest(
            fn() => Http::timeout(120)
                ->attach(
                    'voice_audio',
                    $contents,
                    $audio->getClientOriginalName()
                )
                ->post(
                    $this->baseUrl . '/voice/recognize',
                    [
                        'registered_voices' => $registeredVoicesJson,
                    ]
                ),
            'The AI service could not recognize the voice.'
        );
    }

    /**
     * Read an uploaded file safely.
     */
    protected function readFile(UploadedFile $file): string
    {
        $contents = file_get_contents($file->getRealPath());

        if ($contents === false) {
            throw new RuntimeException(
                'Unable to read the uploaded file.'
            );
        }

        return $contents;
    }

    /**
     * Encode payload arrays as JSON.
     */
    protected function encodePayload(
        array $payload,
        string $description
    ): string {
        try {
            return json_encode(
                $payload,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            throw new RuntimeException(
                'Unable to encode ' . $description . '.',
                0,
                $e
            );
        }
    }

    /**
     * Send an AI request and validate its response.
     */
    protected function sendRequest(
        callable $request,
        string $errorMessage
    ): array {
        try {
            $response = $request();

            if ($response->failed()) {
                $detail = $response->json('detail');

                Log::warning('BioVerify AI request failed', [
                    'status' => $response->status(),
                    'detail' => is_string($detail) ? $detail : null,
                ]);

                // 413/422 mean the sample itself was rejected (too quiet,
                // too short, no face...). Keep that status as the
                // exception code so callers can show the reason.
                throw new RuntimeException(
                    is_string($detail) && $detail !== ''
                        ? $detail
                        : $errorMessage,
                    in_array($response->status(), [413, 422], true)
                        ? 422
                        : 0
                );
            }

            $data = $response->json();

            if (!is_array($data)) {
                throw new RuntimeException(
                    'The AI service returned an invalid JSON response.'
                );
            }

            return $data;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('BioVerify AI connection failed', [
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException(
                'Cannot connect to BioVerify AI. Check that the Python service is running.',
                0,
                $e
            );
        }
    }
}
