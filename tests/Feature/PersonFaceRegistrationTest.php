<?php

namespace Tests\Feature;

use App\Models\PersonFace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BuildsBiometricFixtures;
use Tests\TestCase;

class PersonFaceRegistrationTest extends TestCase
{
    use BuildsBiometricFixtures;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        $this->signInAsAdmin();
    }

    public function test_it_registers_a_face_using_the_embedding_contract(): void
    {
        $person = $this->makePerson();

        Http::fake([
            $this->aiUrl('/face/embedding') => Http::response([
                'success' => true,
                'faces_detected' => 1,
                'embedding' => $this->fakeEmbedding(512),
                'embedding_length' => 512,
                'model_version' => 'buffalo_l',
            ]),
        ]);

        $this->post(route('people.faces.store', $person), [
            'face_image' => $this->fakeFaceImage(),
            'is_primary' => '1',
        ])->assertRedirect(route('people.show', $person));

        Http::assertSent(fn (Request $request) =>
            $request->url() === $this->aiUrl('/face/embedding')
            && in_array('face_image', $this->multipartNames($request), true));

        $face = PersonFace::sole();
        $this->assertSame($person->id, $face->person_id);
        $this->assertSame('buffalo_l', $face->model_version);
        $this->assertTrue($face->is_primary);
        $this->assertCount(512, json_decode($face->face_embedding, true));
        // Face images are private, never on the public disk.
        Storage::disk('local')->assertExists($face->image_path);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_guests_cannot_register_faces(): void
    {
        $person = $this->makePerson();
        auth()->logout();
        Http::fake();

        $this->post(route('people.faces.store', $person), [
            'face_image' => $this->fakeFaceImage(),
        ])->assertRedirect(route('login'));

        Http::assertNothingSent();
    }

    public function test_a_face_already_enrolled_under_another_person_is_rejected(): void
    {
        $existing = $this->makePerson();
        $this->addFace($existing, $this->fakeEmbedding(512));
        $person = $this->makePerson();

        Http::fake([
            $this->aiUrl('/face/embedding') => Http::response([
                'success' => true,
                'faces_detected' => 1,
                'embedding' => $this->fakeEmbedding(512),
                'model_version' => 'buffalo_l',
            ]),
        ]);

        $this->post(route('people.faces.store', $person), [
            'face_image' => $this->fakeFaceImage(),
        ])->assertSessionHasErrors('face_image');

        $this->assertSame(0, $person->faces()->count());
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_it_rejects_a_response_without_exactly_one_face(): void
    {
        $person = $this->makePerson();

        Http::fake([
            $this->aiUrl('/face/embedding') => Http::response([
                'success' => true,
                'faces_detected' => 2,
                'embedding' => $this->fakeEmbedding(512),
            ]),
        ]);

        $this->post(route('people.faces.store', $person), [
            'face_image' => $this->fakeFaceImage(),
        ])->assertSessionHasErrors('face_image');

        $this->assertSame(0, PersonFace::count());
    }

    public function test_it_rejects_an_embedding_of_the_wrong_size(): void
    {
        $person = $this->makePerson();

        Http::fake([
            $this->aiUrl('/face/embedding') => Http::response([
                'success' => true,
                'faces_detected' => 1,
                'embedding' => $this->fakeEmbedding(128),
            ]),
        ]);

        $this->post(route('people.faces.store', $person), [
            'face_image' => $this->fakeFaceImage(),
        ])->assertSessionHasErrors('face_image');

        $this->assertSame(0, PersonFace::count());
    }

    public function test_ai_service_errors_surface_as_validation_errors(): void
    {
        $person = $this->makePerson();

        Http::fake([
            $this->aiUrl('/face/embedding') => Http::response(['detail' => 'No face detected.'], 422),
        ]);

        $this->post(route('people.faces.store', $person), [
            'face_image' => $this->fakeFaceImage(),
        ])->assertSessionHasErrors(['face_image' => 'No face detected.']);

        $this->assertSame(0, PersonFace::count());
    }
}
