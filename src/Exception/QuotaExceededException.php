<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class QuotaExceededException extends HttpException
{
    public function __construct(
        string $message = 'Vous avez atteint votre limite mensuelle de documents',
        ?\Throwable $previous = null
    ) {
        parent::__construct(429, $message, $previous);
    }
}
