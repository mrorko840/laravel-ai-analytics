<?php

use Illuminate\Support\Facades\Route;
use Mrorko840\AiAnalytics\Http\Controllers\DashboardController;
use Mrorko840\AiAnalytics\Http\Controllers\ChatController;
use Mrorko840\AiAnalytics\Http\Controllers\ReportController;
use Mrorko840\AiAnalytics\Http\Controllers\DataSourceController;
use Mrorko840\AiAnalytics\Http\Controllers\DiagnosticsController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('ai-analytics.dashboard');

Route::get('/chat', [ChatController::class, 'index'])->name('ai-analytics.chat');
Route::post('/chat', [ChatController::class, 'store'])->name('ai-analytics.chat.store');
Route::post('/chat/{chatId}/message', [ChatController::class, 'message'])->name('ai-analytics.chat.message');

Route::get('/reports', [ReportController::class, 'index'])->name('ai-analytics.reports');
Route::get('/reports/{id}', [ReportController::class, 'show'])->name('ai-analytics.reports.show');
Route::post('/reports', [ReportController::class, 'store'])->name('ai-analytics.reports.store');
Route::get('/reports/{id}/export/{format}', [ReportController::class, 'export'])->name('ai-analytics.reports.export');

Route::get('/data-sources', [DataSourceController::class, 'index'])->name('ai-analytics.data-sources');
Route::post('/data-sources/toggle', [DataSourceController::class, 'toggle'])->name('ai-analytics.data-sources.toggle');

Route::resource('cards', \Mrorko840\AiAnalytics\Http\Controllers\CardController::class)->names('ai-analytics.cards');

Route::get('/diagnostics', [DiagnosticsController::class, 'index'])->name('ai-analytics.diagnostics');
