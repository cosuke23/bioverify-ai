<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureVerifiedPerson;
use App\Models\Person;
use App\Models\PersonVoice;
use App\Models\RecognitionLog;
use App\Services\BioVerifyAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class VoiceRecognitionController extends Controller
{
    protected BioVerifyAIService $bioVerifyAI;

    public function __construct(BioVerifyAIService $bioVerifyAI)
    {
        $this->bioVerifyAI = $bioVerifyAI;
    }

    /**
     * Verify a voice recorded directly from the microphone.
     */
    public function recognize(Request $request): JsonResponse
    {
        $request->validate([
            'voice_audio' => [
                'required',
                'file',
                'mimes:webm,wav,mp3,m4a,ogg,mp4',
                'max:10240',
            ],
        ]);

        try {
            /*
             * Load registered voice samples that already have embeddings.
             * Each embedding must belong to a person.
             */
            $voiceSamples = PersonVoice::query()
                ->with('person')
                ->whereNotNull('voice_embedding')
                ->where(
                    'model_version',
                    config('services.bioverify_ai.voice_model_version')
                )
                ->whereHas('person', fn ($query) => $query->where('status', 'Active'))
                ->get();

            $registeredVoices = [];

            foreach ($voiceSamples as $voice) {
                $person = $voice->person;

                if (!$person || empty($voice->voice_embedding)) {
                    continue;
                }

                $embedding = $voice->voice_embedding;

                // Support either an array cast or a JSON string.
                if (is_string($embedding)) {
                    $embedding = json_decode($embedding, true);
                }

                if (!is_array($embedding) || empty($embedding)) {
                    continue;
                }

                $registeredVoices[] = [
                    'person_id' => (string) $person->id,
                    'embedding' => array_values($embedding),
                ];
            }

            if (empty($registeredVoices)) {
                return response()->json([
                    'matched' => false,
                    'message' => 'No registered voice samples were found for the current voice model. Register (or re-record) a voice sample first.',
                ], 422);
            }

            $result = $this->bioVerifyAI->recognizeVoice(
                $request->file('voice_audio'),
                $registeredVoices
            );

            // Scores are only shown to signed-in administrators; they
            // would let an attacker tune a replayed or synthetic voice.
            $score = $request->user()
                ? ['similarity_score' => $result['similarity_score'] ?? null]
                : [];

            if (!($result['matched'] ?? false) || empty($result['person_id'])) {
                RecognitionLog::record(
                    'Voice',
                    'Not Matched',
                    null,
                    $result['similarity_score'] ?? null,
                    $request->ip(),
                    $result['message'] ?? 'Voice did not match any registered person.'
                );

                return response()->json([
                    'matched' => false,
                    'message' => $result['message']
                        ?? 'No matching registered voice was found.',
                ] + $score);
            }

            $person = Person::find($result['person_id']);

            if (!$person) {
                return response()->json([
                    'matched' => false,
                    'message' => 'A voice match was detected, but the associated person record could not be found.',
                ], 404);
            }

            // The status may have changed while the AI was matching.
            if ($person->status !== 'Active') {
                RecognitionLog::record(
                    'Voice',
                    'Not Matched',
                    $person->id,
                    $result['similarity_score'] ?? null,
                    $request->ip(),
                    'Matched an inactive personnel record; access refused.'
                );

                return response()->json([
                    'matched' => false,
                    'message' => 'This personnel record is inactive and cannot be verified.',
                ], 403);
            }

            RecognitionLog::record(
                'Voice',
                'Matched',
                $person->id,
                $result['similarity_score'] ?? null,
                $request->ip(),
                'Voice successfully recognized using ' .
                    ($result['model_version'] ?? 'unknown model') . '.'
            );

            EnsureVerifiedPerson::markVerified($request, $person->id);

            return response()->json([
                'matched' => true,
                'redirect_url' => route('recognized.person', $person),
                'person_id' => $person->id,
                'name' => $person->full_name
                    ?? $person->name
                    ?? 'Registered Person',
                'person' => [
                    'id' => $person->id,
                    'person_id' => $person->id,
                    'name' => $person->full_name
                        ?? $person->name
                        ?? 'Registered Person',
                ],
                'message' => 'Voice verification successful.',
            ] + $score);
        } catch (Throwable $e) {
            Log::error('Voice recognition failed', [
                'error' => $e->getMessage(),
            ]);

            RecognitionLog::record(
                'Voice',
                'Not Matched',
                null,
                null,
                $request->ip(),
                'Attempt failed: ' . $e->getMessage()
            );

            if ($e->getCode() === 422) {
                return response()->json([
                    'matched' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return response()->json([
                'matched' => false,
                'message' => 'Voice verification failed. Please try again.',
            ], 500);
        }
    }
}
