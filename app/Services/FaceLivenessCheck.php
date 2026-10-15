<?php

namespace App\Services;

use Illuminate\Http\Request;

/**
 * Capture guidance and liveness for the face verification kiosk.
 *
 * Each camera frame analysed by the AI service is turned into simple
 * checks (one face, centred, distance, light, focus) plus guidance for
 * the person. Frames that pass those checks also feed a liveness state
 * kept in the server-side session:
 *
 *   1. look   - face the camera
 *   2. turn   - turn the head to either side
 *   3. return - face the camera again
 *
 * and at least `min_live_frames` good frames must score as live with the
 * passive anti-spoofing model. Only then may a guest run recognition, and
 * a pass is single-use. This deters printed photos and phone screens; it
 * is not a certified presentation-attack detection system.
 */
class FaceLivenessCheck
{
    private const KEY = 'face_liveness';

    public function evaluate(Request $request, array $analysis): array
    {
        $capture = config('services.bioverify_ai.face_capture');
        $live = config('services.bioverify_ai.face_liveness');

        $face = $analysis['face'] ?? null;
        $count = (int) ($analysis['faces_detected'] ?? 0);

        $checks = [
            'face' => $count >= 1,
            'single' => $count === 1,
            'centered' => false,
            'distance' => false,
            'lighting' => false,
            'focus' => false,
        ];

        if ($face) {
            $offset = max(
                abs(($face['center_x'] ?? 0.5) - 0.5),
                abs(($face['center_y'] ?? 0.5) - 0.48)
            );

            $checks['centered'] = $offset <= $capture['max_center_offset'];
            $checks['distance'] = $face['width'] >= $capture['min_face_width']
                && $face['width'] <= $capture['max_face_width'];
            $checks['lighting'] = $face['brightness'] >= $capture['min_brightness']
                && $face['brightness'] <= $capture['max_brightness'];
            $checks['focus'] = $face['sharpness'] >= $capture['min_sharpness'];
        }

        $qualityOk = !in_array(false, $checks, true);

        $state = $this->state($request);

        if ($qualityOk) {
            $state = $this->advance($state, $face, $analysis['liveness'] ?? null, $live);
        }

        $request->session()->put(self::KEY, $state);

        $passed = $state['passed_at'] !== null;

        return [
            'faces_detected' => $count,
            'checks' => $checks,
            'quality_ok' => $qualityOk,
            'guidance' => $this->guidance($checks, $face, $capture, $state, $passed),
            'liveness' => [
                'required' => (bool) $live['required'],
                'stage' => $passed ? 'passed' : $state['stage'],
                'live_frames' => min($state['live_frames'], $live['min_live_frames']),
                'live_frames_needed' => $live['min_live_frames'],
                'passed' => $passed,
            ],
            'ready' => $qualityOk && ($passed || !$live['required']),
            // Frame box for drawing the face outline (normalised 0-1).
            'box' => $face ? [
                'x' => $face['center_x'] - $face['width'] / 2,
                'y' => $face['center_y'] - $face['height'] / 2,
                'w' => $face['width'],
                'h' => $face['height'],
            ] : null,
        ];
    }

    /**
     * True when this session completed the liveness check recently.
     */
    public function passed(Request $request): bool
    {
        $state = $request->session()->get(self::KEY);
        $validFor = config('services.bioverify_ai.face_liveness.valid_seconds');

        return is_array($state)
            && ($state['passed_at'] ?? null) !== null
            && now()->timestamp - $state['passed_at'] <= $validFor;
    }

    /**
     * A pass is single-use: clear it after any recognition attempt.
     */
    public function reset(Request $request): void
    {
        $request->session()->forget(self::KEY);
    }

    private function state(Request $request): array
    {
        $state = $request->session()->get(self::KEY);
        $attemptSeconds = config('services.bioverify_ai.face_liveness.attempt_seconds');

        $fresh = [
            'started_at' => now()->timestamp,
            'stage' => 'look',
            'live_frames' => 0,
            'passed_at' => null,
        ];

        if (!is_array($state)) {
            return $fresh;
        }

        // Start over if the attempt has gone on too long.
        if (now()->timestamp - ($state['started_at'] ?? 0) > $attemptSeconds) {
            return $fresh;
        }

        return $state;
    }

    private function advance(array $state, array $face, ?array $liveness, array $live): array
    {
        if ($state['passed_at'] !== null) {
            return $state;
        }

        if (($liveness['is_live'] ?? false) === true
            && ($liveness['live_score'] ?? 0) >= $live['threshold']) {
            $state['live_frames']++;
        }

        $yaw = abs((float) ($face['yaw'] ?? 0));

        $state['stage'] = match ($state['stage']) {
            'look' => $yaw <= $live['yaw_frontal'] ? 'turn' : 'look',
            'turn' => $yaw >= $live['yaw_turned'] ? 'return' : 'turn',
            'return' => $yaw <= $live['yaw_frontal'] ? 'done' : 'return',
            default => $state['stage'],
        };

        if ($state['stage'] === 'done' && $state['live_frames'] >= $live['min_live_frames']) {
            $state['passed_at'] = now()->timestamp;
        }

        return $state;
    }

    private function guidance(array $checks, ?array $face, array $capture, array $state, bool $passed): array
    {
        $say = fn (string $code, string $message, string $tone = 'action') =>
            compact('code', 'message', 'tone');

        if (!$checks['face']) {
            return $say('no_face', 'Position your face inside the frame.');
        }

        if (!$checks['single']) {
            return $say('multiple_faces', 'Only one person should be in view.', 'warn');
        }

        if ($face['width'] < $capture['min_face_width']) {
            return $say('too_far', 'Move closer to the camera.');
        }

        if ($face['width'] > $capture['max_face_width']) {
            return $say('too_close', 'Move back slightly.');
        }

        if (!$checks['centered']) {
            return $say('off_center', 'Center your face inside the oval.');
        }

        if ($face['brightness'] < $capture['min_brightness']) {
            return $say('too_dark', 'More light needed. Face a light source.', 'warn');
        }

        if ($face['brightness'] > $capture['max_brightness']) {
            return $say('too_bright', 'Too much glare. Step away from direct light.', 'warn');
        }

        if (!$checks['focus']) {
            return $say('blurry', 'Hold still for a sharp image.');
        }

        if ($passed) {
            return $say('ready', 'Liveness confirmed. Hold still.', 'ok');
        }

        return match ($state['stage']) {
            'look' => $say('look', 'Look straight at the camera.'),
            'turn' => $say('turn', 'Slowly turn your head to the left or right.'),
            'return' => $say('return', 'Now face the camera again.'),
            default => $say('verifying_liveness', 'Hold still while we confirm liveness.', 'ok'),
        };
    }
}
