# Laravel AI Analytics

A **UNIVERSAL AI-powered analytics and reporting package** for Laravel that functions as an intelligent, conversational ChatGPT layer over your exact database schema.

## Overview

Unlike traditional analytics packages that force you into fixed tables (like `users`, `orders`, `transactions`), AI Analytics scans your database dynamically. It allows the admin to **Enable** tables and columns and automatically adapts to your schema format!

This gives your Laravel project:
1. **Dynamic Dashboard Cards:** Create widgets visually. Map them to any table using COUNT, SUM, AVG, MAX, MIN aggregations instantaneously.
2. **Conversational SQL Engine:** Ask business intelligence questions in English or Bangla. The system securely generates READ-ONLY SQL on the fly, executes it against configured tables, and returns the natural language summary.
3. **QueryGuard Safety:** The AI-generated SQL is rigidly intercepted. Unsafe modifications (`INSERT`, `UPDATE`, `DROP`) are permanently blocked by the `QueryGuardService` enforcing strict `SELECT` usage.
4. **Data Sources UI:** Visually inspect your database connection's columns, indexes, and structures to decide which data is exposed to the AI model.
5. **Comprehensive Reporting:** Export queried formats dynamically to PDF, CSV, JSON, and HTML.

## Installation

1. Install the package via composer:
```bash
composer require mrorko840/laravel-ai-analytics
```

2. Run the interactive install process to publish configurations and run the built-in database migrations (includes tracking for data sources, dynamic cards, and AI history).
```bash
php artisan ai-analytics:install
```

## Security & Execution Architecture

**Zero Raw Execution Risk**: The AI queries are heavily monitored.
AI Analytics uses a completely safe, deterministic process:
1. User provides a business question (`"Total users joined this month?"`).
2. AI extracts intent and matches it against **only** the tables you toggled 'Active' in Data Sources.
3. LLM returns a raw SQL String.
4. `QueryGuardService` analyzes the string for structural integrity and destructible blocks.
5. System securely hits your database connection dynamically with `DB::select()`.
6. Extracted data array is returned to the LLM to summarize natively.
7. End-User reads a perfect English intelligence report natively without knowing the SQL logic applied.

## Setup & Setup Flow

1. Navigate to `/ai-analytics/data-sources` locally inside your Laravel Application.
2. Enable specific tables such as **users**, **deposits**, and **products**.
3. Create manual visual Cards on the Dashboard picking columns + Aggregations (e.g., `SUM(amount)` on `withdrawals`).
4. Jump into the Chat assistant and converse with your data freely.

## System Diagnostics

Visit `/ai-analytics/diagnostics` at any time to check:
- Package module health status.
- Connected Database connection validations.
- Total Enabled Data Sources injected into context.
- UI Configuration settings.

## Extending the LLM Provider

While OpenAI is recommended default (`AI_ANALYTICS_PROVIDER=openai`), you may configure alternative API Keys dynamically utilizing `.env`:

```env
AI_ANALYTICS_PROVIDER=openai
AI_ANALYTICS_MODEL=gpt-4o
AI_ANALYTICS_API_KEY=sk-...
```

## Testing

Ensure PHPUnit tests pass correctly:
```bash
./vendor/bin/phpunit
```

## License

The MIT License (MIT).
