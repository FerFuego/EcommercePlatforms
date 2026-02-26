<?php

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/chatbot/message', [ChatbotController::class, 'message']);
Route::post('/mercadopago/webhook', [PaymentWebhookController::class, 'handleMercadoPago']);
