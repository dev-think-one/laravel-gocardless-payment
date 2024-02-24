<?php

use Illuminate\Support\Facades\Route;

Route::post('webhook', [\GoCardlessPayment\Http\Controllers\WebhookController::class, 'handleWebhook'])->name('webhook');
