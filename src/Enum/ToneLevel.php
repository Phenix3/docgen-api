<?php

declare(strict_types=1);

namespace App\Enum;

enum ToneLevel: string
{
    case VERY_FORMAL = 'very_formal';
    case FORMAL = 'formal';
    case SIMPLE = 'simple';

    public function label(): string
    {
        return match($this) {
            self::VERY_FORMAL => 'Très formel',
            self::FORMAL => 'Formel standard',
            self::SIMPLE => 'Simple',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::VERY_FORMAL => 'Pour les administrations officielles et ministères',
            self::FORMAL => 'Pour les entreprises et services publics',
            self::SIMPLE => 'Pour les communications courantes',
        };
    }
}
