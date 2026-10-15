<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Authenticatable so a person can be signed in through the `people`
 * guard (config/auth.php) after biometric verification.
 */
class Person extends Authenticatable
{
    protected $table = 'people';

    protected $fillable = [
        'person_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'mobile_number',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function faces(): HasMany
    {
        return $this->hasMany(PersonFace::class);
    }

    public function voices(): HasMany
    {
        return $this->hasMany(PersonVoice::class);
    }

    public function recognitionLogs(): HasMany
    {
        return $this->hasMany(RecognitionLog::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim(
            $this->first_name . ' ' .
                ($this->middle_name ? $this->middle_name . ' ' : '') .
                $this->last_name .
                ($this->suffix ? ' ' . $this->suffix : '')
        );
    }
}
