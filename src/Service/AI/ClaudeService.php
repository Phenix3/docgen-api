<?php

declare(strict_types=1);

namespace App\Service\AI;

use App\Exception\AIGenerationException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ClaudeService implements AIServiceInterface
{
    private const API_URL = 'https://api.anthropic.com/v1/messages';
    private const MODEL = 'claude-3-5-sonnet-20240620';
    private const MAX_TOKENS = 1024;
    private const TIMEOUT = 60;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly string $apiKey
    ) {}

    public function generate(string $systemPrompt, string $userPrompt): AIResponse
    {
        $startTime = microtime(true);

        try {
            $this->logger->info('Starting AI generation', [
                'system_prompt_length' => strlen($systemPrompt),
                'user_prompt_length' => strlen($userPrompt),
            ]);

            $response = $this->httpClient->request('POST', self::API_URL, [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ],
                'timeout' => self::TIMEOUT,
                'json' => [
                    'model' => self::MODEL,
                    'max_tokens' => self::MAX_TOKENS,
                    'system' => $systemPrompt,
                    'messages' => [
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $data = $response->toArray();
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            if ($statusCode !== 200) {
                throw new \RuntimeException(
                    $data['error']['message'] ?? 'Unknown API error'
                );
            }

            $content = $data['content'][0]['text'] ?? '';
            $tokensUsed = ($data['usage']['input_tokens'] ?? 0) + ($data['usage']['output_tokens'] ?? 0);

            $this->logger->info('AI generation completed', [
                'tokens_used' => $tokensUsed,
                'duration_ms' => $durationMs,
                'content_length' => strlen($content),
            ]);

            return new AIResponse(
                content: $content,
                tokensUsed: $tokensUsed,
                durationMs: $durationMs
            );

        } catch (\Throwable $e) {
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);
            
            $this->logger->error('AI generation failed', [
                'error' => $e->getMessage(),
                'duration_ms' => $durationMs,
            ]);

            throw new AIGenerationException(
                'Erreur lors de la génération: ' . $e->getMessage(),
                $e
            );
        }
    }
}
