<?php

namespace Tests\Feature;

use App\Models\PersonVoice;
use App\Models\RecognitionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BuildsBiometricFixtures;
use Tests\TestCase;

class VoiceRecognitionTest extends TestCase
{
    use BuildsBiometricFixtures;
    use RefreshDatabase;

    private int $size;

    private string $model;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        $this->size = config('services.bioverify_ai.voice_embedding_size');
        $this->model = config('services.bioverify_ai.voice_model_version');
    }

    public function test_voice_registration_requires_an_administrator(): void
    {
        $person = $this->makePerson();
        Http::fake();

        $this->post(route('people.voices.store', $person), [
            'voice_audio' => $this->fakeVoiceAudio(),
        ])->assertRedirect(route('login'));

        Http::assertNothingSent();
    }

    public function test_voice_registration_uses_the_embedding_contract(): void
    {
        $this->signInAsAdmin();
        $person = $this->makePerson();

        Http::fake([
            $this->aiUrl('/voice/embedding') => Http::response([
                'success' => true,
                'embedding' => $this->fakeEmbedding($this->size),
                'embedding_length' => $this->size,
                'model_version' => $this->model,
            ]),
        ]);

        $this->post(route('people.voices.store', $person), [
            'voice_audio' => $this->fakeVoiceAudio(),
        ])->assertRedirect(route('recognized.person', $person));

        Http::assertSent(fn (Request $request) =>
            $request->url() === $this->aiUrl('/voice/embedding')
            && in_array('voice_audio', $this->multipartNames($request), true));

        $voice = PersonVoice::sole();
        $this->assertSame($this->model, $voice->model_version);
        $this->assertCount($this->size, json_decode($voice->voice_embedding, true));

        // Recordings are private, never on the public disk.
        Storage::disk('local')->assertExists($voice->audio_path);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_voice_registration_rejects_an_embedding_of_the_wrong_size(): void
    {
        $this->signInAsAdmin();
        $person = $this->makePerson();

        Http::fake([
            $this->aiUrl('/voice/embedding') => Http::response([
                'success' => true,
                'embedding' => $this->fakeEmbedding(512),
                'model_version' => $this->model,
            ]),
        ]);

        $this->post(route('people.voices.store', $person), [
            'voice_audio' => $this->fakeVoiceAudio(),
        ])->assertSessionHasErrors('voice_audio');

        $this->assertSame(0, PersonVoice::count());
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_voice_registration_shows_why_a_sample_was_rejected(): void
    {
        $this->signInAsAdmin();
        $person = $this->makePerson();

        Http::fake([
            $this->aiUrl('/voice/embedding') => Http::response(
                ['detail' => 'Audio is too quiet. Speak closer to the microphone and try again.'],
                422
            ),
        ]);

        $this->post(route('people.voices.store', $person), [
            'voice_audio' => $this->fakeVoiceAudio(),
        ])->assertSessionHasErrors([
            'voice_audio' => 'Audio is too quiet. Speak closer to the microphone and try again.',
        ]);
    }

    public function test_voice_recognition_returns_422_without_registered_voices(): void
    {
        Http::fake();

        $this->postJson(route('voice.recognition.recognize'), [
            'voice_audio' => $this->fakeVoiceAudio(),
        ])
            ->assertStatus(422)
            ->assertJsonPath('matched', false);

        Http::assertNothingSent();
    }

    public function test_samples_from_an_older_voice_model_are_not_compared(): void
    {
        $person = $this->makePerson();
        $voice = $this->addVoice($person, $this->fakeEmbedding(192));
        $voice->update(['model_version' => 'speechbrain-ecapa-voxceleb-v1']);

        Http::fake();

        $this->postJson(route('voice.recognition.recognize'), [
            'voice_audio' => $this->fakeVoiceAudio(),
        ])->assertStatus(422);

        Http::assertNothingSent();
    }

    public function test_voice_recognition_sends_the_expected_contract(): void
    {
        $person = $this->makePerson();
        $this->addVoice($person);

        Http::fake([
            $this->aiUrl('/voice/recognize') => Http::response([
                'success' => true,
                'matched' => true,
                'person_id' => (string) $person->id,
                'similarity_score' => 0.77,
                'model_version' => $this->model,
            ]),
        ]);

        $this->postJson(route('voice.recognition.recognize'), [
            'voice_audio' => $this->fakeVoiceAudio(),
        ])
            ->assertOk()
            ->assertJsonPath('matched', true)
            ->assertJsonMissingPath('similarity_score')
            ->assertJsonPath('redirect_url', route('recognized.person', $person))
            ->assertJsonPath('person.id', $person->id)
            ->assertJsonPath('name', $person->full_name);

        Http::assertSent(function (Request $request) use ($person) {
            $registered = $this->multipartJson($request, 'registered_voices');

            return $request->url() === $this->aiUrl('/voice/recognize')
                && in_array('voice_audio', $this->multipartNames($request), true)
                && $registered[0]['person_id'] === (string) $person->id
                && count($registered[0]['embedding']) === $this->size;
        });

        $this->get(route('recognized.person', $person))->assertOk();
    }

    public function test_a_voice_match_writes_a_recognition_log(): void
    {
        $person = $this->makePerson();
        $this->addVoice($person);

        Http::fake([
            $this->aiUrl('/voice/recognize') => Http::response([
                'success' => true,
                'matched' => true,
                'person_id' => (string) $person->id,
                'similarity_score' => 0.71234,
                'model_version' => $this->model,
            ]),
        ]);

        $this->postJson(route('voice.recognition.recognize'), [
            'voice_audio' => $this->fakeVoiceAudio(),
        ])->assertOk();

        $log = RecognitionLog::sole();
        $this->assertSame($person->id, $log->person_id);
        $this->assertSame('Voice', $log->recognition_type);
        $this->assertSame('Matched', $log->result);
        $this->assertSame('0.71234', $log->voice_score);
        $this->assertNull($log->face_score);
    }

    public function test_inactive_people_are_not_sent_as_voice_candidates(): void
    {
        $active = $this->makePerson();
        $this->addVoice($active);
        $this->addVoice($this->makePerson(['status' => 'Inactive']));

        Http::fake([
            $this->aiUrl('/voice/recognize') => Http::response(['success' => true, 'matched' => false]),
        ]);

        $this->postJson(route('voice.recognition.recognize'), [
            'voice_audio' => $this->fakeVoiceAudio(),
        ])->assertOk();

        Http::assertSent(function (Request $request) use ($active) {
            $ids = array_column($this->multipartJson($request, 'registered_voices'), 'person_id');

            return $ids === [(string) $active->id];
        });
    }

    public function test_a_voice_match_for_a_person_deactivated_during_matching_is_rejected(): void
    {
        $person = $this->makePerson();
        $this->addVoice($person);

        Http::fake(function () use ($person) {
            $person->update(['status' => 'Inactive']);

            return Http::response([
                'success' => true,
                'matched' => true,
                'person_id' => (string) $person->id,
                'similarity_score' => 0.9,
            ]);
        });

        $this->postJson(route('voice.recognition.recognize'), [
            'voice_audio' => $this->fakeVoiceAudio(),
        ])
            ->assertStatus(403)
            ->assertJsonPath('matched', false);

        $this->assertSame('Not Matched', RecognitionLog::sole()->result);
        $this->get(route('recognized.person', $person))
            ->assertRedirect(route('biometric.login'));
    }

    public function test_a_voice_non_match_is_logged_and_hides_the_score(): void
    {
        $person = $this->makePerson();
        $this->addVoice($person);

        Http::fake([
            $this->aiUrl('/voice/recognize') => Http::response([
                'success' => true,
                'matched' => false,
                'person_id' => null,
                'similarity_score' => 0.4,
                'message' => 'No matching registered voice was found.',
            ]),
        ]);

        $this->postJson(route('voice.recognition.recognize'), [
            'voice_audio' => $this->fakeVoiceAudio(),
        ])
            ->assertOk()
            ->assertJsonPath('matched', false)
            ->assertJsonMissingPath('similarity_score');

        $log = RecognitionLog::sole();
        $this->assertSame('Not Matched', $log->result);
        $this->assertSame('0.40000', $log->voice_score);
    }

    public function test_voice_recognition_hides_internal_errors(): void
    {
        $person = $this->makePerson();
        $this->addVoice($person);

        Http::fake([
            $this->aiUrl('/voice/recognize') => Http::response(['detail' => 'Not Found'], 404),
        ]);

        $this->postJson(route('voice.recognition.recognize'), [
            'voice_audio' => $this->fakeVoiceAudio(),
        ])
            ->assertStatus(500)
            ->assertJsonPath('matched', false)
            ->assertJsonPath('message', 'Voice verification failed. Please try again.');
    }
}
