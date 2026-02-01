<?php

declare(strict_types=1);

namespace App\Service\AI;

final readonly class AIResponse
{
    public function __construct(
        public string $content,
        public int $tokensUsed,
        public int $durationMs,
        public bool $success = true,
        public ?string $error = null
    ) {}

    public static function failure(string $error, int $durationMs = 0): self
    {
        return new self(
            content: '',
            tokensUsed: 0,
            durationMs: $durationMs,
            success: false,
            error: $error
        );
    }
}
