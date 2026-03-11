<?php

namespace Mrorko840\AiAnalytics\Chat;

use Mrorko840\AiAnalytics\Contracts\AiProviderInterface;

class InsightFormatter
{
    protected AiProviderInterface $ai;

    public function __construct(AiProviderInterface $ai)
    {
        $this->ai = $ai;
    }

    public function format(string $userQuestion, array $metricResults): string
    {
        $systemMessages = [
            "You are an expert business analytics assistant.",
            "Analyze the given data and provide a helpful, human-readable summary that answers the user's question.",
            "Highlight key insights and trends without exposing any row-level sensitive data.",
            "Keep the response concise and professional."
        ];

        $prompt = "User Question: " . $userQuestion . "\n\n";
        $prompt .= "Metric Data:\n" . json_encode($metricResults, JSON_PRETTY_PRINT) . "\n\n";
        $prompt .= "Please provide an insightful summary and response based strictly on the data above.";

        return $this->ai->ask($prompt, $systemMessages);
    }
}
