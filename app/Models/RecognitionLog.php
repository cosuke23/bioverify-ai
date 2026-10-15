<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecognitionLog extends Model
{
    protected $fillable = [
        'person_id',
        'recognition_type',
        'face_score',
        'voice_score',
        'combined_score',
        'result',
        'source',
        'ip_address',
        'remarks',
    ];

    protected $casts = [
        'face_score' => 'decimal:5',
        'voice_score' => 'decimal:5',
        'combined_score' => 'decimal:5',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Audit one face or voice attempt. Failures are recorded too, so
     * repeated guessing or spoofing attempts leave a trail.
     */
    public static function record(
        string $type,
        string $result,
        ?int $personId,
        mixed $score,
        ?string $ip,
        string $remarks
    ): void {
        try {
            static::create([
                'person_id' => $personId,
                'recognition_type' => $type,
                'face_score' => $type === 'Face' && is_numeric($score) ? $score : null,
                'voice_score' => $type === 'Voice' && is_numeric($score) ? $score : null,
                'combined_score' => null,
                'result' => $result,
                'source' => $type . ' Recognition',
                'ip_address' => $ip,
                'remarks' => mb_substr($remarks, 0, 1000),
            ]);
        } catch (\Throwable $e) {
            // Never let audit logging break verification itself.
            report($e);
        }
    }
}