<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\PersonFace;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PersonFaceController extends Controller
{
    public function create(Person $person): View
    {
        return view('people.faces.create', compact('person'));
    }

    public function store(
        Request $request,
        Person $person
    ): RedirectResponse {
        $validated = $request->validate([
            'face_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'is_primary' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Generate face embedding using Python AI service
        |--------------------------------------------------------------------------
        */

        $file = $request->file('face_image');

        try {
            $aiResponse = Http::timeout(60)
                ->attach(
                    'face_image',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )
                ->post(
                    rtrim(
                        config('services.bioverify_ai.url'),
                        '/'
                    ) . '/face/embedding'
                );

            if ($aiResponse->failed()) {
                $detail = $aiResponse->json('detail');

                $message = is_string($detail) && $detail !== ''
                    ? $detail
                    : 'The AI service could not process the face image.';

                return back()
                    ->withInput()
                    ->withErrors([
                        'face_image' => $message,
                    ]);
            }

            $aiResult = $aiResponse->json();

            if (!($aiResult['success'] ?? false)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'face_image' => 'The AI service could not generate a face embedding.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Validate AI result
            |--------------------------------------------------------------------------
            */

            if (($aiResult['faces_detected'] ?? 0) !== 1) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'face_image' => 'Exactly one face must be detected in the image.',
                    ]);
            }

            $embedding = $aiResult['embedding'] ?? null;

            if (!is_array($embedding) || count($embedding) !== 512) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'face_image' => 'Invalid face embedding returned by the AI service.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Reject a face already enrolled under another person
            |--------------------------------------------------------------------------
            */

            $duplicateOf = $this->findOtherPersonWithFace($person, $embedding);

            if ($duplicateOf) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'face_image' => 'This face already appears to be registered to ' .
                            $duplicateOf->full_name . ' (' . $duplicateOf->person_id . '). ' .
                            'Check that you are enrolling the right person.',
                    ]);
            }
        } catch (ConnectionException $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'face_image' => 'Unable to connect to the BioVerify AI service. Please make sure the AI service is running.',
                ]);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'face_image' => 'The face image could not be processed. Please try again.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Save image, then the record (cleaning up the file on failure)
        |--------------------------------------------------------------------------
        */

        $path = $file->store(
            'people/' . $person->id . '/faces',
            'local'
        );

        if (!$path) {
            return back()
                ->withInput()
                ->withErrors([
                    'face_image' => 'The face image could not be saved.',
                ]);
        }

        $isPrimary = $request->boolean('is_primary');

        try {
            DB::transaction(function () use ($person, $path, $embedding, $aiResult, $isPrimary) {
                if ($isPrimary) {
                    $person->faces()->update([
                        'is_primary' => false,
                    ]);
                }

                PersonFace::create([
                    'person_id' => $person->id,
                    'image_path' => $path,
                    'face_embedding' => json_encode($embedding),
                    'model_version' => $aiResult['model_version'] ?? 'buffalo_l',
                    'is_primary' => $isPrimary,
                ]);
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);

            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'face_image' => 'The face sample could not be saved. Please try again.',
                ]);
        }

        return redirect()
            ->route('people.show', $person)
            ->with(
                'success',
                'Face sample successfully registered and AI embedding generated.'
            );
    }

    /**
     * Return another active or inactive person whose enrolled face
     * matches this embedding at the recognition threshold, if any.
     */
    private function findOtherPersonWithFace(Person $person, array $embedding): ?Person
    {
        $threshold = (float) config('services.bioverify_ai.face_match_threshold');

        $query = $this->normalize($embedding);

        if ($query === null) {
            return null;
        }

        $faces = PersonFace::query()
            ->where('person_id', '!=', $person->id)
            ->whereNotNull('face_embedding')
            ->with('person')
            ->get();

        foreach ($faces as $face) {
            $stored = $this->normalize(json_decode($face->face_embedding, true));

            if ($stored === null || count($stored) !== count($query)) {
                continue;
            }

            $score = 0.0;

            foreach ($query as $i => $value) {
                $score += $value * $stored[$i];
            }

            if ($score >= $threshold && $face->person) {
                return $face->person;
            }
        }

        return null;
    }

    private function normalize(mixed $vector): ?array
    {
        if (!is_array($vector) || $vector === []) {
            return null;
        }

        $norm = sqrt(array_sum(array_map(fn ($v) => (float) $v * (float) $v, $vector)));

        if ($norm <= 0) {
            return null;
        }

        return array_map(fn ($v) => (float) $v / $norm, array_values($vector));
    }

    public function destroy(
        Person $person,
        PersonFace $face
    ): RedirectResponse {
        if ($face->person_id !== $person->id) {
            abort(404);
        }

        if ($face->image_path) {
            Storage::disk('local')->delete($face->image_path);
        }

        $face->delete();

        return redirect()
            ->route('people.show', $person)
            ->with('success', 'Face sample successfully removed.');
    }
}
