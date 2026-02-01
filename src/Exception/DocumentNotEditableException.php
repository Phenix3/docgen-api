<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class DocumentNotEditableException extends HttpException
{
    public function __construct(
        string $message = 'Ce document ne peut pas être modifié',
        ?\Throwable $previous = null
    ) {
        parent::__construct(400, $message, $previous);
    }
}
