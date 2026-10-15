<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\PersonVoice;
use App\Services\BioVerifyAIService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PersonVoiceController extends Controller
{
    protected BioVerifyAIService $bioVerifyAI;

    public function __construct(BioVerifyAIService $bioVerifyAI)
    {
        $this->bioVerifyAI = $bioVerifyAI;
    }

    /**
     * Display the voice registration page.
     */
    public function create(Person $person): View
    {
        return view(
            'people.voices.create',
            compact('person')
        );
    }

    /**
     * Generate an embedding and register a voice sample.
     */
    public function store(
        Request $request,
        Person $person
    ): RedirectResponse {
        $request->validate([
            'voice_audio' => [
                'required',
                'file',
                'mimes:wav,mp3,m4a,ogg,webm,mp4',
                'max:10240',
            ],
            'is_primary' => [
                'nullable',
                'boolean',
            ],
        ]);

        $file = $request->file('voice_audio');
        $path = null;

        try {
            // Generate the embedding using the Python AI service.
            $result = $this->bioVerifyAI
                ->generateVoiceEmbedding($file);

            $embedding = $result['embedding'] ?? null;

            $modelVersion = config('services.bioverify_ai.voice_model_version');

            if (
                !is_array($embedding) ||
                count($embedding) !== config('services.bioverify_ai.voice_embedding_size') ||
                ($result['model_version'] ?? null) !== $modelVersion
            ) {
                throw ValidationException::withMessages([
                    'voice_audio' => 'The AI service returned an invalid voice embedding. Please record the sample again.',
                ]);
            }

            // Ensure all embedding values are numeric and finite.
            foreach ($embedding as $value) {
                if (
                    !is_numeric($value) ||
                    !is_finite((float) $value)
                ) {
                    throw ValidationException::withMessages([
                        'voice_audio' => 'The generated voice embedding contains invalid values.',
                    ]);
                }
            }

            // Store audio only after embedding generation succeeds.
            $path = $file->store(
                'people/' . $person->id . '/voices',
                'local'
            );

            if (!$path) {
                throw new \RuntimeException(
                    'Unable to store the voice recording.'
                );
            }

            $isPrimary = $request->boolean('is_primary');

            DB::transaction(function () use (
                $person,
                $path,
                $embedding,
                $modelVersion,
                $isPrimary
            ) {
                if ($isPrimary) {
                    $person->voices()->update([
                        'is_primary' => false,
                    ]);
                }

                PersonVoice::create([
                    'person_id' => $person->id,
                    'audio_path' => $path,
                    'voice_embedding' => json_encode(
                        array_values($embedding),
                        JSON_THROW_ON_ERROR
                    ),
                    'model_version' => $modelVersion,
                    'is_primary' => $isPrimary,
                ]);
            });

            return redirect()
                ->route('recognized.person', $person)
                ->with(
                    'success',
                    'Voice sample successfully registered.'
                );
        } catch (ValidationException $e) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }

            throw $e;
        } catch (Throwable $e) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }

            if ($e->getCode() !== 422) {
                report($e);
            }

            return back()
                ->withInput()
                ->withErrors([
                    'voice_audio' => $e->getCode() === 422
                        ? $e->getMessage()
                        : 'Voice registration failed. Please try again and ensure the AI service is running.',
                ]);
        }
    }

    /**
     * Delete a registered voice sample.
     */
    public function destroy(
        Person $person,
        PersonVoice $voice
    ): RedirectResponse {
        if ((string) $voice->person_id !== (string) $person->id) {
            abort(404);
        }

        DB::transaction(function () use ($voice) {
            $voice->delete();
        });

        if ($voice->audio_path) {
            Storage::disk('local')->delete(
                $voice->audio_path
            );
        }

        return redirect()
            ->route('recognized.person', $person)
            ->with(
                'success',
                'Voice sample successfully removed.'
            );
    }
}
