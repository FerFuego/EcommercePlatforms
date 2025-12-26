<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\OpenAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Tests\TestCase;

class ChatbotIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock OpenAI facade by default to avoid real API calls
        OpenAI::fake();
    }

    /** @test */
    public function it_returns_a_successful_ai_response()
    {
        $user = User::factory()->create();

        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Esta es una respuesta simulada del asistente.',
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/chatbot/message', [
                'messages' => [
                    ['role' => 'user', 'content' => 'Hola, ¿cómo estás?']
                ]
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Esta es una respuesta simulada del asistente.',
            ]);
    }

    /** @test */
    public function it_validates_message_format()
    {
        $user = User::factory()->create();

        // Falta el campo 'role' en uno de los mensajes
        $response = $this->actingAs($user)
            ->postJson('/api/chatbot/message', [
                'messages' => [
                    ['content' => 'Hola']
                ]
            ]);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Invalid message format.']);
    }

    /** @test */
    public function it_handles_openai_quota_errors_gracefully()
    {
        $user = User::factory()->create();

        // Simulamos una excepción de OpenAI
        OpenAI::fake([
            new \Exception('insufficient_quota', 429),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/chatbot/message', [
                'messages' => [
                    ['role' => 'user', 'content' => 'Cualquier pregunta']
                ]
            ]);

        $response->assertStatus(200) // Devolvemos 200 con un mensaje de error amigable en el JSON
            ->assertJson([
                'message' => 'El asistente está recibiendo muchas consultas en este momento. Por favor, intenta nuevamente en unos segundos.',
            ]);
    }

    /** @test */
    public function open_ai_service_includes_system_prompt()
    {
        $service = new OpenAIService();

        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Respuesta',
                        ],
                    ],
                ],
            ]),
        ]);

        $service->getChatResponse([
            ['role' => 'user', 'content' => 'Test']
        ]);

        OpenAI::assertSent(\OpenAI\Resources\Chat::class, function (string $method, array $parameters) {
            return $method === 'create' &&
                $parameters['model'] === 'gpt-4o-mini' &&
                $parameters['messages'][0]['role'] === 'system' &&
                str_contains($parameters['messages'][0]['content'], 'Eres el asistente oficial');
        });
    }
}
