<?php

namespace Mrorko840\AiAnalytics\Contracts;

interface AiProviderInterface
{
    /**
     * Send a prompt to the AI and get a response text.
     */
    public function ask(string $prompt, array $systemMessages = []): string;

    /**
     * Parse structured intent from the AI.
     */
    public function parseIntent(string $input, array $context = []): array;
}
