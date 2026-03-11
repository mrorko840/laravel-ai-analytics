<?php

use Illuminate\Support\Facades\Route;
use Mrorko840\AiAnalytics\Http\Controllers\DashboardController;
use Mrorko840\AiAnalytics\Http\Controllers\ChatController;
use Mrorko840\AiAnalytics\Http\Controllers\ReportController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('ai-analytics.dashboard');

Route::get('/chat', [ChatController::class, 'index'])->name('ai-analytics.chat');
Route::post('/chat', [ChatController::class, 'store'])->name('ai-analytics.chat.store');
Route::post('/chat/{chatId}/message', [ChatController::class, 'message'])->name('ai-analytics.chat.message');

Route::get('/reports', [ReportController::class, 'index'])->name('ai-analytics.reports');
Route::get('/reports/{id}', [ReportController::class, 'show'])->name('ai-analytics.reports.show');
Route::post('/reports', [ReportController::class, 'store'])->name('ai-analytics.reports.store');
Route::get('/reports/{id}/export/{format}', [ReportController::class, 'export'])->name('ai-analytics.reports.export');
