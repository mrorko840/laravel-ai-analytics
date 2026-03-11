<?php

use Illuminate\Support\Facades\Route;
use Mrorko840\AiAnalytics\Http\Controllers\ChatController;
use Mrorko840\AiAnalytics\Http\Controllers\ReportController;

Route::post('/chat', [ChatController::class, 'apiMessage'])->name('ai-analytics.api.chat');
Route::post('/reports/export', [ReportController::class, 'apiExport'])->name('ai-analytics.api.reports.export');
