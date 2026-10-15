<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureVerifiedPerson;
use App\Models\PersonFace;
use App\Models\PersonVoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves face images and voice recordings from the private disk.
 *
 * Only an administrator, or the person currently verified by
 * biometric login, may fetch a person's samples.
 */
class BiometricMediaController extends Controller
{
    public const DISK = 'local';

    public function face(Request $request, PersonFace $face): Response
    {
        return $this->serve($request, $face->person_id, $face->image_path);
    }

    public function voice(Request $request, PersonVoice $voice): Response
    {
        return $this->serve($request, $voice->person_id, $voice->audio_path);
    }

    private function serve(Request $request, int $personId, ?string $path): Response
    {
        if (
            !$request->user()
            && !EnsureVerifiedPerson::isVerified($request, $personId)
        ) {
            abort(403);
        }

        if (!$path || !Storage::disk(self::DISK)->exists($path)) {
            abort(404);
        }

        return Storage::disk(self::DISK)->response($path, null, [
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
