<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureVerifiedPerson;
use App\Models\Person;
use App\Models\PersonFace;
use App\Models\RecognitionLog;
use App\Services\BioVerifyAIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class FaceRecognitionController extends Controller
{
    public function recognize(
        Request $request,
        BioVerifyAIService $aiService
    ): JsonResponse {
        $validated = $request->validate([
            'face_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Get registered face embeddings
        |--------------------------------------------------------------------------
        */

        $registeredFaces = PersonFace::query()
            ->whereNotNull('face_embedding')
            ->where('face_embedding', '!=', '')
            ->whereHas('person', fn ($query) => $query->where('status', 'Active'))
            ->with(
                'person:id,person_id,first_name,middle_name,last_name,suffix'
            )
            ->get()
            ->map(function ($face) {

                if (!$face->person) {
                    return null;
                }

                $embedding = json_decode(
                    $face->face_embedding,
                    true
                );

                if (!is_array($embedding)) {
                    return null;
                }

                if (count($embedding) !== 512) {
                    return null;
                }

                return [
                    'person_id' => $face->person->person_id,
                    'embedding' => $embedding,
                ];
            })
            ->filter()
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | No registered embeddings
        |--------------------------------------------------------------------------
        */

        if (empty($registeredFaces)) {
            return response()->json([
                'success' => false,
                'message' => 'No registered face embeddings are available.',
            ], 422);
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Send image to Python AI service
            |--------------------------------------------------------------------------
            */

            $result = $aiService->recognizeFace(
                $validated['face_image'],
                $registeredFaces
            );

            /*
            |--------------------------------------------------------------------------
            | Successful AI match
            |--------------------------------------------------------------------------
            */

            if (
                ($result['matched'] ?? false) === true &&
                !empty($result['person_id'])
            ) {

                $person = Person::query()
                    ->where(
                        'person_id',
                        $result['person_id']
                    )
                    ->where('status', 'Active')
                    ->first();

                /*
                |--------------------------------------------------------------------------
                | Make sure the personnel exists
                |--------------------------------------------------------------------------
                */

                if (!$person) {

                    return response()->json([
                        'success' => false,
                        'matched' => false,
                        'message' =>
                            'The recognized personnel record could not be found in the database.',
                    ], 404);
                }

                /*
                |--------------------------------------------------------------------------
                | CREATE RECOGNITION LOG
                |--------------------------------------------------------------------------
                */

                RecognitionLog::create([
                    'person_id' => $person->id,

                    'recognition_type' => 'Face',

                    'face_score' =>
                        $result['similarity_score'] ?? null,

                    'voice_score' => null,

                    'combined_score' => null,

                    'result' => 'Matched',

                    'source' => 'Face Recognition',

                    'ip_address' => $request->ip(),

                    'remarks' =>
                        'Face successfully recognized by BioVerify AI using ' .
                        ($result['model_version'] ?? 'unknown model') .
                        '.',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Return personnel information to frontend
                |--------------------------------------------------------------------------
                */

                $result['person'] = [
                    'id' => $person->id,
                    'person_id' => $person->person_id,
                    'name' => $person->full_name,
                ];

                EnsureVerifiedPerson::markVerified($request, $person->id);

                $result['redirect_url'] = route('recognized.person', $person);
            } else {
                RecognitionLog::record(
                    'Face',
                    'Not Matched',
                    null,
                    $result['similarity_score'] ?? null,
                    $request->ip(),
                    $result['message'] ?? 'Face did not match any registered person.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Return response
            |--------------------------------------------------------------------------
            */

            // Similarity scores, thresholds and boxes help an attacker
            // tune a spoof, so only signed-in administrators see them.
            if (!$request->user()) {
                $result = Arr::only($result, [
                    'success',
                    'matched',
                    'message',
                    'person',
                    'redirect_url',
                ]);
            }

            return response()->json($result);

        } catch (\Throwable $e) {

            // 422 = the sample was rejected (no face, poor quality); not a fault.
            if ($e->getCode() !== 422) {
                report($e);
            }

            RecognitionLog::record(
                'Face',
                'Not Matched',
                null,
                null,
                $request->ip(),
                'Attempt failed: ' . $e->getMessage()
            );

            return response()->json([
                'success' => false,
                'message' => $e->getCode() === 422
                    ? $e->getMessage()
                    : 'Face verification is temporarily unavailable. Please try again.',
            ], $e->getCode() === 422 ? 422 : 503);
        }
    }
}