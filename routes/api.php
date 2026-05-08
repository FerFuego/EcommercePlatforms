<?php

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/chatbot/message', [ChatbotController::class, 'message']);
Route::post('/mercadopago/webhook', [PaymentWebhookController::class, 'handleMercadoPago'])->middleware('mp_signature');

// WhatsApp Cloud API Webhooks
Route::get('/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify'])->name('api.whatsapp.verify');
Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle'])->name('api.whatsapp.handle');


