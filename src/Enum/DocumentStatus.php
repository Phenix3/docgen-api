<?php

declare(strict_types=1);

namespace App\Enum;

enum DocumentStatus: string
{
    case DRAFT = 'draft';
    case GENERATING = 'generating';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Brouillon',
            self::GENERATING => 'Génération en cours',
            self::COMPLETED => 'Terminé',
            self::FAILED => 'Échec',
        };
    }

    public function isEditable(): bool
    {
        return $this === self::DRAFT || $this === self::FAILED;
    }
}
