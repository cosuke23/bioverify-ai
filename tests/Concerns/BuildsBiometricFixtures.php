<?php

namespace Tests\Concerns;

use App\Models\Person;
use App\Models\PersonFace;
use App\Models\PersonVoice;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;

/**
 * Synthetic fixtures for biometric contract tests.
 *
 * Embeddings are constant vectors, never real biometric data.
 */
trait BuildsBiometricFixtures
{
    protected function aiUrl(string $path = ''): string
    {
        return rtrim(config('services.bioverify_ai.url'), '/') . $path;
    }

    protected function fakeEmbedding(int $size, float $value = 0.01): array
    {
        return array_fill(0, $size, $value);
    }

    protected function makePerson(array $attributes = []): Person
    {
        static $sequence = 0;
        $sequence++;

        return Person::create(array_merge([
            'person_id' => 'TEST-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'first_name' => 'Test',
            'last_name' => 'Person' . $sequence,
            'status' => 'Active',
        ], $attributes));
    }

    protected function addFace(Person $person, ?array $embedding = null): PersonFace
    {
        return PersonFace::create([
            'person_id' => $person->id,
            'image_path' => 'people/' . $person->id . '/faces/test.jpg',
            'face_embedding' => json_encode($embedding ?? $this->fakeEmbedding(512)),
            'model_version' => 'buffalo_l',
            'is_primary' => true,
        ]);
    }

    protected function addVoice(Person $person, ?array $embedding = null): PersonVoice
    {
        return PersonVoice::create([
            'person_id' => $person->id,
            'audio_path' => 'people/' . $person->id . '/voices/test.wav',
            'voice_embedding' => json_encode(
                $embedding ?? $this->fakeEmbedding(config('services.bioverify_ai.voice_embedding_size'))
            ),
            'model_version' => config('services.bioverify_ai.voice_model_version'),
            'is_primary' => true,
        ]);
    }

    /**
     * Personnel, enrollment, PDF and chat routes require an admin.
     */
    protected function signInAsAdmin(): User
    {
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin' . uniqid() . '@example.test',
            'password' => 'test-password-123',
        ]);

        $this->actingAs($admin);

        return $admin;
    }

    protected function fakeFaceImage(): UploadedFile
    {
        return UploadedFile::fake()->create('face.jpg', 20, 'image/jpeg');
    }

    protected function fakeVoiceAudio(): UploadedFile
    {
        // WAV matches what the registration page uploads. A declared
        // audio/webm type would be guessed as "weba" and fail validation.
        return UploadedFile::fake()->create('voice.wav', 20, 'audio/wav');
    }

    /**
     * Names of the multipart parts in an outgoing HTTP request.
     */
    protected function multipartNames(Request $request): array
    {
        return collect($request->data())->pluck('name')->all();
    }

    /**
     * Decoded JSON contents of a named multipart field.
     */
    protected function multipartJson(Request $request, string $name): mixed
    {
        $part = collect($request->data())->firstWhere('name', $name);

        return $part ? json_decode($part['contents'], true) : null;
    }
}
