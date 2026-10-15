<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BiometricMediaController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PersonFaceController;
use App\Http\Controllers\PersonVoiceController;
use App\Http\Controllers\BioVerifyAITestController;
use App\Http\Controllers\FaceRecognitionController;
use App\Http\Controllers\FaceRecognitionPageController;
use App\Http\Controllers\VoiceRecognitionController;
use App\Http\Controllers\RecognizedPersonController;
use App\Http\Controllers\BioVerifyChatController;
use App\Http\Controllers\PdfDocumentController;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Administrator Sign In
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Biometric Login (public)
|--------------------------------------------------------------------------
*/

Route::view('/biometric-login', 'biometric-login')
    ->name('biometric.login');

Route::middleware('throttle:recognition')->group(function () {
    Route::post(
        '/face-recognition/recognize',
        [FaceRecognitionController::class, 'recognize']
    )->name('face.recognition.recognize');

    Route::post(
        '/voice-recognition/recognize',
        [VoiceRecognitionController::class, 'recognize']
    )->name('voice.recognition.recognize');
});

/*
|--------------------------------------------------------------------------
| Recognized Personnel (administrator or the verified person)
|--------------------------------------------------------------------------
*/

Route::get(
    '/recognized-person/{person}',
    [RecognizedPersonController::class, 'show']
)->middleware('verified.person')->name('recognized.person');

// Biometric samples live on the private disk; the controller checks
// that the viewer is an administrator or the verified owner.
Route::get(
    '/biometrics/faces/{face}',
    [BiometricMediaController::class, 'face']
)->name('biometrics.face');

Route::get(
    '/biometrics/voices/{voice}',
    [BiometricMediaController::class, 'voice']
)->name('biometrics.voice');

/*
|--------------------------------------------------------------------------
| Administrator Area
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |----------------------------------------------------------------------
    | Personnel Management
    |----------------------------------------------------------------------
    */

    Route::resource('people', PersonController::class);

    /*
    |----------------------------------------------------------------------
    | Face Registration
    |----------------------------------------------------------------------
    */

    Route::get(
        'people/{person}/faces/create',
        [PersonFaceController::class, 'create']
    )->name('people.faces.create');

    Route::post(
        'people/{person}/faces',
        [PersonFaceController::class, 'store']
    )->name('people.faces.store');

    Route::delete(
        'people/{person}/faces/{face}',
        [PersonFaceController::class, 'destroy']
    )->name('people.faces.destroy');

    /*
    |----------------------------------------------------------------------
    | Voice Registration
    |----------------------------------------------------------------------
    */

    Route::get(
        'people/{person}/voices/create',
        [PersonVoiceController::class, 'create']
    )->name('people.voices.create');

    Route::post(
        'people/{person}/voices',
        [PersonVoiceController::class, 'store']
    )->name('people.voices.store');

    Route::delete(
        'people/{person}/voices/{voice}',
        [PersonVoiceController::class, 'destroy']
    )->name('people.voices.destroy');

    /*
    |----------------------------------------------------------------------
    | Ask Pong AI and AI Service Health
    |----------------------------------------------------------------------
    */

    Route::get('/ai-chat', [BioVerifyChatController::class, 'index'])
        ->name('ai-chat.index');

    Route::post('/ai-chat/message', [BioVerifyChatController::class, 'send'])
        ->middleware('throttle:ai')
        ->name('ai-chat.send');

    Route::get(
        '/ai/health',
        [BioVerifyAITestController::class, 'health']
    )->name('ai.health');

    /*
    |----------------------------------------------------------------------
    | Face Recognition test page
    |----------------------------------------------------------------------
    */

    Route::get(
        '/face-recognition',
        [FaceRecognitionPageController::class, 'index']
    )->name('face.recognition.index');

    /*
    |----------------------------------------------------------------------
    | PDF Documents
    |----------------------------------------------------------------------
    */

    Route::get('/pdf-documents', [PdfDocumentController::class, 'index'])
        ->name('pdf-documents.index');

    Route::post('/pdf-documents', [PdfDocumentController::class, 'store'])
        ->middleware('throttle:ai')
        ->name('pdf-documents.store');

    Route::get('/pdf-documents/{pdfDocument}', [PdfDocumentController::class, 'show'])
        ->name('pdf-documents.show');

    Route::post('/pdf-documents/{pdfDocument}/ask', [PdfDocumentController::class, 'ask'])
        ->middleware('throttle:ai')
        ->name('pdf-documents.ask');

    Route::post('/pdf-documents/{pdfDocument}/retry', [PdfDocumentController::class, 'retry'])
        ->name('pdf-documents.retry');

    Route::delete('/pdf-documents/{pdfDocument}', [PdfDocumentController::class, 'destroy'])
        ->name('pdf-documents.destroy');
});
