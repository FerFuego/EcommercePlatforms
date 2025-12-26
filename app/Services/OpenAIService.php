<?php
namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected string $systemPrompt;

    public function __construct()
    {
        // ⚠️ DOCUMENTACIÓN RESUMIDA (NO TODO EL MD)
        $documentationSummary = <<<DOC
        La plataforma permite:
        - Registro de usuarios mediante formulario simple.
        - Creación de pedidos seleccionando productos disponibles.
        - Publicación y venta de viandas.
        - Gestión de entregas y tiempos.
        - Buenas prácticas: calidad, presentación y cumplimiento de horarios.
        DOC;

        $this->systemPrompt = <<<PROMPT
            Eres el asistente oficial de una plataforma Cocinarte.

            Ayudas a los usuarios con:
            - Registro
            - Creación de pedidos
            - Uso de pantallas
            - Soporte funcional
            - Consejos prácticos de negocio para vender mejor

            REGLAS:
            - No inventes información.
            - Responde solo con el contexto dado.
            - Siempre en español.
            - Tono claro, cercano y profesional.
            - Explicaciones simples, paso a paso.
            - No menciones tecnologías ni que eres una IA.

            CONTEXTO:
            {$documentationSummary}
            PROMPT;
    }

    public function getChatResponse(array $messages): ?string
    {
        try {
            $chatMessages = array_merge([
                [
                    'role' => 'system',
                    'content' => $this->systemPrompt
                ],
            ], $messages);

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => $chatMessages,
                'temperature' => 0.3,
                'max_tokens' => 500,
            ]);

            return $response->choices[0]->message->content ?? null;
        } catch (\Throwable $e) {
            Log::error('OpenAI API Error', [
                'message' => $e->getMessage(),
            ]);

            if (str_contains($e->getMessage(), 'rate') || str_contains($e->getMessage(), 'quota')) {
                return 'El asistente está recibiendo muchas consultas en este momento. Por favor, intenta nuevamente en unos segundos.';
            }

            return 'No pude procesar tu consulta en este momento. Intenta más tarde.';
        }
    }

    protected function getDocumentation(): string
    {
        $docPath = base_path('HUMANS.md');
        if (file_exists($docPath)) {
            return file_get_contents($docPath);
        }
        return 'No hay documentación adicional disponible.';
    }
}
