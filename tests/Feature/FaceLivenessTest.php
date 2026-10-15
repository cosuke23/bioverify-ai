<?php

namespace Tests\Feature;

use App\Models\RecognitionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\BuildsBiometricFixtures;
use Tests\TestCase;

class FaceLivenessTest extends TestCase
{
    use BuildsBiometricFixtures;
    use RefreshDatabase;

    /**
     * A good-quality, centred frame from the analyze endpoint.
     */
    private function frame(float $yaw, float $liveScore = 0.95, array $face = []): array
    {
        return [
            'success' => true,
            'faces_detected' => 1,
            'face' => array_merge([
                'detection_score' => 0.9,
                'center_x' => 0.5,
                'center_y' => 0.48,
                'width' => 0.3,
                'height' => 0.45,
                'yaw' => $yaw,
                'brightness' => 140.0,
                'sharpness' => 400.0,
            ], $face),
            'liveness' => [
                'status' => 'ok',
                'is_live' => $liveScore >= 0.8,
                'live_score' => $liveScore,
                'threshold' => 0.8,
            ],
            'processing_time_ms' => 120,
        ];
    }

    private function analyze()
    {
        return $this->postJson(route('face.recognition.analyze'), [
            'face_image' => $this->fakeFaceImage(),
        ]);
    }

    private function recognize()
    {
        return $this->postJson(route('face.recognition.recognize'), [
            'face_image' => $this->fakeFaceImage(),
        ]);
    }

    private function fakeAi(array $frames, array $recognize = ['success' => true, 'matched' => false]): void
    {
        $sequence = Http::sequence();

        foreach ($frames as $frame) {
            $sequence->push($frame);
        }

        Http::fake([
            $this->aiUrl('/face/analyze') => $sequence,
            $this->aiUrl('/face/recognize') => Http::response($recognize),
        ]);
    }

    public function test_guests_cannot_verify_without_completing_liveness(): void
    {
        $this->addFace($this->makePerson());
        Http::fake();

        $this->recognize()
            ->assertStatus(428)
            ->assertJsonPath('code', 'liveness_required');

        Http::assertNothingSent();
        $this->assertSame('Not Matched', RecognitionLog::sole()->result);
    }

    public function test_guidance_for_guests_hides_raw_model_scores(): void
    {
        $this->fakeAi([$this->frame(0.0, 0.93)]);

        $this->analyze()
            ->assertOk()
            ->assertJsonPath('quality_ok', true)
            ->assertJsonPath('guidance.code', 'turn')
            ->assertJsonPath('liveness.stage', 'turn')
            ->assertJsonMissingPath('diagnostics')
            ->assertJsonMissing(['live_score' => 0.93]);
    }

    public function test_administrators_receive_diagnostics(): void
    {
        $this->signInAsAdmin();
        $this->fakeAi([$this->frame(0.0, 0.93)]);

        $this->analyze()
            ->assertOk()
            ->assertJsonPath('diagnostics.liveness.live_score', 0.93);
    }

    public function test_poor_quality_frames_give_guidance_and_do_not_count(): void
    {
        $this->fakeAi([
            $this->frame(0.0, 0.95, ['brightness' => 40.0]),
            $this->frame(0.0, 0.95, ['width' => 0.08]),
            ['success' => true, 'faces_detected' => 2, 'face' => $this->frame(0.0)['face'], 'liveness' => null],
            ['success' => true, 'faces_detected' => 0, 'face' => null, 'liveness' => null],
        ]);

        $this->analyze()->assertJsonPath('guidance.code', 'too_dark')->assertJsonPath('liveness.stage', 'look');
        $this->analyze()->assertJsonPath('guidance.code', 'too_far')->assertJsonPath('liveness.stage', 'look');
        $this->analyze()->assertJsonPath('guidance.code', 'multiple_faces');
        $this->analyze()->assertJsonPath('guidance.code', 'no_face')->assertJsonPath('ready', false);
    }

    public function test_head_turn_and_live_frames_unlock_one_verification(): void
    {
        $person = $this->makePerson();
        $this->addFace($person);

        $this->fakeAi(
            [$this->frame(0.02), $this->frame(0.41), $this->frame(0.05)],
            ['success' => true, 'matched' => true, 'person_id' => $person->person_id, 'similarity_score' => 0.9]
        );

        $this->analyze()->assertJsonPath('liveness.stage', 'turn');
        $this->analyze()->assertJsonPath('liveness.stage', 'return');
        $this->analyze()
            ->assertJsonPath('liveness.passed', true)
            ->assertJsonPath('ready', true)
            ->assertJsonPath('guidance.code', 'ready');

        $this->recognize()
            ->assertOk()
            ->assertJsonPath('matched', true)
            ->assertJsonPath('redirect_url', route('recognized.person', $person));

        // Single use: a second attempt needs a new liveness check.
        $this->recognize()->assertStatus(428);
    }

    public function test_a_photo_that_never_scores_live_cannot_pass(): void
    {
        $this->addFace($this->makePerson());

        // Even with a perfect head turn, frames that are not live fail.
        $this->fakeAi([$this->frame(0.0, 0.3), $this->frame(0.45, 0.3), $this->frame(0.0, 0.3)]);

        $this->analyze();
        $this->analyze();
        $this->analyze()
            ->assertJsonPath('liveness.passed', false)
            ->assertJsonPath('ready', false);

        $this->recognize()->assertStatus(428);
    }

    public function test_administrators_in_the_console_are_exempt(): void
    {
        $this->signInAsAdmin();
        $this->addFace($this->makePerson());
        $this->fakeAi([]);

        $this->recognize()->assertOk();
    }
}
