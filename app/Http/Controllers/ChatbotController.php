<?php

namespace App\Http\Controllers;

use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    protected OpenAIService $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function message(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'messages' => 'required|array',
            'messages.*.role' => 'required|string|in:user,assistant',
            'messages.*.content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid message format.'], 400);
        }

        $response = $this->openAIService->getChatResponse($request->messages);

        if (!$response) {
            return response()->json(['error' => 'Failed to get response from AI.'], 500);
        }

        return response()->json([
            'message' => $response,
        ]);
    }
}
