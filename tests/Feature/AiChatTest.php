<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\BuildsBiometricFixtures;
use Tests\TestCase;

class AiChatTest extends TestCase
{
    use BuildsBiometricFixtures;
    use RefreshDatabase;

    private function chatUrl(): string
    {
        return rtrim(config('services.bioverify_chat.url'), '/') . '/chat';
    }

    public function test_guests_cannot_use_the_chat(): void
    {
        Http::fake();

        $this->postJson(route('ai-chat.send'), ['message' => 'Hi'])
            ->assertUnauthorized();

        Http::assertNothingSent();
    }

    public function test_it_forwards_the_message_and_returns_answer_and_sources(): void
    {
        $this->signInAsAdmin();

        Http::fake([
            $this->chatUrl() => Http::response([
                'answer' => 'Hello from Pong.',
                'sources' => [['title' => 'Overview']],
            ]),
        ]);

        $this->postJson(route('ai-chat.send'), ['message' => '  Hi Pong  '])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'answer' => 'Hello from Pong.',
                'sources' => [['title' => 'Overview']],
            ]);

        Http::assertSent(fn (Request $request) =>
            $request->url() === $this->chatUrl()
            && $request['message'] === 'Hi Pong');
    }

    public function test_it_validates_the_message(): void
    {
        $this->signInAsAdmin();

        Http::fake();

        $this->postJson(route('ai-chat.send'), ['message' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors('message');

        Http::assertNothingSent();
    }

    public function test_it_returns_503_without_leaking_service_details(): void
    {
        $this->signInAsAdmin();

        Http::fake([
            $this->chatUrl() => Http::response(['detail' => 'Ollama HTTP 500: internal trace'], 500),
        ]);

        $this->postJson(route('ai-chat.send'), ['message' => 'Hi'])
            ->assertStatus(503)
            ->assertJsonPath('success', false)
            ->assertJsonMissing(['message' => 'Ollama HTTP 500: internal trace']);
    }
}
