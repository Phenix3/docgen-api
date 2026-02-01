<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AIGenerationException extends HttpException
{
    public function __construct(
        string $message = 'Erreur lors de la génération du document',
        ?\Throwable $previous = null
    ) {
        parent::__construct(503, $message, $previous);
    }
}
