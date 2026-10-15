<?php

namespace Tests\Feature;

use App\Models\RecognitionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\BuildsBiometricFixtures;
use Tests\TestCase;

class FaceRecognitionTest extends TestCase
{
    use BuildsBiometricFixtures;
    use RefreshDatabase;

    public function test_it_returns_422_when_no_faces_are_registered(): void
    {
        Http::fake();

        $this->postJson(route('face.recognition.recognize'), [
            'face_image' => $this->fakeFaceImage(),
        ])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        Http::assertNothingSent();
    }

    public function test_it_sends_the_expected_contract_to_the_ai_service(): void
    {
        $person = $this->makePerson();
        $this->addFace($person);

        Http::fake([
            $this->aiUrl('/face/recognize') => Http::response([
                'success' => true,
                'matched' => false,
                'person_id' => null,
                'similarity_score' => 0.2,
                'threshold' => 0.5,
                'model_version' => 'buffalo_l',
            ]),
        ]);

        $this->postJson(route('face.recognition.recognize'), [
            'face_image' => $this->fakeFaceImage(),
        ])->assertOk();

        Http::assertSent(function (Request $request) use ($person) {
            $registered = $this->multipartJson($request, 'registered_faces');

            return $request->url() === $this->aiUrl('/face/recognize')
                && in_array('face_image', $this->multipartNames($request), true)
                && is_array($registered)
                && $registered[0]['person_id'] === $person->person_id
                && count($registered[0]['embedding']) === 512;
        });
    }

    public function test_a_match_returns_person_details_and_writes_a_log(): void
    {
        $person = $this->makePerson();
        $this->addFace($person);

        Http::fake([
            $this->aiUrl('/face/recognize') => Http::response([
                'success' => true,
                'matched' => true,
                'person_id' => $person->person_id,
                'similarity_score' => 0.81234,
                'threshold' => 0.5,
                'model_version' => 'buffalo_l',
            ]),
        ]);

        $this->postJson(route('face.recognition.recognize'), [
            'face_image' => $this->fakeFaceImage(),
        ])
            ->assertOk()
            ->assertJsonPath('matched', true)
            ->assertJsonMissingPath('similarity_score')
            ->assertJsonMissingPath('threshold')
            ->assertJsonPath('redirect_url', route('recognized.person', $person))
            ->assertJsonPath('person.id', $person->id)
            ->assertJsonPath('person.person_id', $person->person_id)
            ->assertJsonPath('person.name', $person->full_name);

        $log = RecognitionLog::sole();
        $this->assertSame($person->id, $log->person_id);
        $this->assertSame('Face', $log->recognition_type);
        $this->assertSame('Matched', $log->result);
        $this->assertSame('0.81234', $log->face_score);
    }

    public function test_a_match_opens_only_that_persons_record(): void
    {
        $person = $this->makePerson();
        $other = $this->makePerson();
        $this->addFace($person);

        $this->get(route('recognized.person', $person))
            ->assertRedirect(route('biometric.login'));

        Http::fake([
            $this->aiUrl('/face/recognize') => Http::response([
                'success' => true,
                'matched' => true,
                'person_id' => $person->person_id,
                'similarity_score' => 0.9,
            ]),
        ]);

        $this->postJson(route('face.recognition.recognize'), [
            'face_image' => $this->fakeFaceImage(),
        ])->assertOk();

        $this->get(route('recognized.person', $person))->assertOk();
        $this->get(route('recognized.person', $other))
            ->assertRedirect(route('biometric.login'));
        $this->get(route('people.show', $person))
            ->assertRedirect(route('login'));
    }

    public function test_administrators_see_similarity_scores(): void
    {
        $this->signInAsAdmin();
        $person = $this->makePerson();
        $this->addFace($person);

        Http::fake([
            $this->aiUrl('/face/recognize') => Http::response([
                'success' => true,
                'matched' => false,
                'person_id' => null,
                'similarity_score' => 0.31,
                'threshold' => 0.5,
            ]),
        ]);

        $this->postJson(route('face.recognition.recognize'), [
            'face_image' => $this->fakeFaceImage(),
        ])
            ->assertOk()
            ->assertJsonPath('similarity_score', 0.31)
            ->assertJsonPath('threshold', 0.5);
    }

    public function test_a_non_match_is_logged_and_hides_the_score(): void
    {
        $person = $this->makePerson();
        $this->addFace($person);

        Http::fake([
            $this->aiUrl('/face/recognize') => Http::response([
                'success' => true,
                'matched' => false,
                'person_id' => null,
                'similarity_score' => 0.31,
                'message' => 'Face detected but no sufficiently similar registered face was found.',
            ]),
        ]);

        $this->postJson(route('face.recognition.recognize'), [
            'face_image' => $this->fakeFaceImage(),
        ])
            ->assertOk()
            ->assertJsonPath('matched', false)
            ->assertJsonMissingPath('similarity_score');

        $log = RecognitionLog::sole();
        $this->assertNull($log->person_id);
        $this->assertSame('Not Matched', $log->result);
        $this->assertSame('0.31000', $log->face_score);
    }

    public function test_inactive_people_are_not_sent_as_candidates(): void
    {
        $active = $this->makePerson();
        $this->addFace($active);
        $this->addFace($this->makePerson(['status' => 'Inactive']));

        Http::fake([
            $this->aiUrl('/face/recognize') => Http::response(['success' => true, 'matched' => false]),
        ]);

        $this->postJson(route('face.recognition.recognize'), [
            'face_image' => $this->fakeFaceImage(),
        ])->assertOk();

        Http::assertSent(function (Request $request) use ($active) {
            $ids = array_column($this->multipartJson($request, 'registered_faces'), 'person_id');

            return $ids === [$active->person_id];
        });
    }

    public function test_only_inactive_faces_counts_as_no_registered_faces(): void
    {
        Http::fake();
        $this->addFace($this->makePerson(['status' => 'Inactive']));

        $this->postJson(route('face.recognition.recognize'), [
            'face_image' => $this->fakeFaceImage(),
        ])->assertStatus(422);

        Http::assertNothingSent();
    }

    public function test_a_match_for_a_person_deactivated_during_matching_is_rejected(): void
    {
        $person = $this->makePerson();
        $this->addFace($person);

        Http::fake(function () use ($person) {
            $person->update(['status' => 'Inactive']);

            return Http::response([
                'success' => true,
                'matched' => true,
                'person_id' => $person->person_id,
                'similarity_score' => 0.9,
            ]);
        });

        $this->postJson(route('face.recognition.recognize'), [
            'face_image' => $this->fakeFaceImage(),
        ])
            ->assertStatus(403)
            ->assertJsonPath('matched', false);

        $this->assertSame('Not Matched', RecognitionLog::sole()->result);
        $this->get(route('recognized.person', $person))
            ->assertRedirect(route('biometric.login'));
    }

    public function test_a_rejected_sample_returns_the_reason(): void
    {
        $person = $this->makePerson();
        $this->addFace($person);

        Http::fake([
            $this->aiUrl('/face/recognize') => Http::response(['detail' => 'No face detected.'], 422),
        ]);

        $this->postJson(route('face.recognition.recognize'), [
            'face_image' => $this->fakeFaceImage(),
        ])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'No face detected.');

        $this->assertSame('Not Matched', RecognitionLog::sole()->result);
    }

    public function test_ai_service_faults_return_503_without_details(): void
    {
        $person = $this->makePerson();
        $this->addFace($person);

        Http::fake([
            $this->aiUrl('/face/recognize') => Http::response(['detail' => 'Traceback: onnx session crashed'], 500),
        ]);

        $this->postJson(route('face.recognition.recognize'), [
            'face_image' => $this->fakeFaceImage(),
        ])
            ->assertStatus(503)
            ->assertJsonPath('success', false)
            ->assertJsonMissing(['message' => 'Traceback: onnx session crashed']);
    }
}
