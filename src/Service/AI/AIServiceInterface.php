<?php

declare(strict_types=1);

namespace App\Service\AI;

interface AIServiceInterface
{
    public function generate(string $systemPrompt, string $userPrompt): AIResponse;
}
