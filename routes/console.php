<?php

use App\Models\PersonFace;
use App\Models\PersonVoice;
use App\Models\User;
use App\Services\BioVerifyAIService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create
    {--name= : Administrator name}
    {--email= : Administrator email}
    {--generate-password : Create a strong random password and print it once}', function () {
    $name = $this->option('name') ?: $this->ask('Name');
    $email = $this->option('email') ?: $this->ask('Email');

    if ($this->option('generate-password')) {
        // Avoids hidden-input prompts, which some Windows terminals
        // (or Smart App Control) cannot provide.
        $password = Str::password(20, symbols: false);
        $confirmation = $password;
    } else {
        $password = $this->secret('Password (min 12 characters)');
        $confirmation = $this->secret('Confirm password');
    }

    $validator = Validator::make(
        compact('name', 'email', 'password') + ['password_confirmation' => $confirmation],
        [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(12)],
        ]
    );

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        if (!$password && !$this->option('generate-password')) {
            $this->line('If the password prompt does not accept input, run:');
            $this->line('  php artisan admin:create --generate-password');
        }

        return 1;
    }

    User::create(compact('name', 'email', 'password'));

    $this->info("Administrator {$email} created. Sign in at /login.");

    if ($this->option('generate-password')) {
        $this->warn('Password (shown only once, store it in your password manager):');
        $this->line($password);
    }
})->purpose('Create an administrator account');

Artisan::command('voices:reembed', function (BioVerifyAIService $bioVerifyAI) {
    $modelVersion = config('services.bioverify_ai.voice_model_version');
    $embeddingSize = config('services.bioverify_ai.voice_embedding_size');

    $voices = PersonVoice::query()
        ->where(function ($query) use ($modelVersion) {
            $query->whereNull('model_version')
                ->orWhere('model_version', '!=', $modelVersion);
        })
        ->get();

    if ($voices->isEmpty()) {
        $this->info("All voice samples already use {$modelVersion}.");

        return;
    }

    foreach ($voices as $voice) {
        $fullPath = Storage::disk('local')->path($voice->audio_path);

        if (!is_file($fullPath)) {
            $this->warn("Voice #{$voice->id}: audio file missing, re-record it.");

            continue;
        }

        try {
            $result = $bioVerifyAI->generateVoiceEmbedding(
                new UploadedFile($fullPath, basename($fullPath), null, null, true)
            );

            $embedding = $result['embedding'] ?? null;

            if (!is_array($embedding) || count($embedding) !== $embeddingSize) {
                throw new RuntimeException('Invalid embedding returned.');
            }

            $voice->update([
                'voice_embedding' => json_encode(array_values($embedding), JSON_THROW_ON_ERROR),
                'model_version' => $modelVersion,
            ]);

            $this->info("Voice #{$voice->id} (person #{$voice->person_id}): re-embedded.");
        } catch (Throwable $e) {
            $this->error("Voice #{$voice->id}: {$e->getMessage()}");
        }
    }
})->purpose('Regenerate stored voice embeddings with the current voice model');

Artisan::command('biometrics:make-private', function () {
    $public = Storage::disk('public');
    $private = Storage::disk('local');

    $paths = PersonFace::pluck('image_path')
        ->merge(PersonVoice::pluck('audio_path'))
        ->filter();

    $moved = 0;

    foreach ($paths as $path) {
        if (!$public->exists($path)) {
            continue;
        }

        if (!$private->exists($path)) {
            $private->writeStream($path, $public->readStream($path));
        }

        $public->delete($path);
        $moved++;
    }

    $this->info("Moved {$moved} biometric file(s) to the private disk.");
})->purpose('Move face images and voice recordings off the public disk');
