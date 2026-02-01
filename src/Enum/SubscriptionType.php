<?php

declare(strict_types=1);

namespace App\Enum;

enum SubscriptionType: string
{
    case FREE = 'free';
    case CITIZEN = 'citizen';
    case PRO = 'pro';
    case ENTERPRISE = 'enterprise';

    public function monthlyLimit(): int
    {
        return match($this) {
            self::FREE => 3,
            self::CITIZEN => 15,
            self::PRO => 999999,
            self::ENTERPRISE => 999999,
        };
    }

    public function canExportDocx(): bool
    {
        return $this !== self::FREE;
    }

    public function label(): string
    {
        return match($this) {
            self::FREE => 'Gratuit',
            self::CITIZEN => 'Citoyen',
            self::PRO => 'Professionnel',
            self::ENTERPRISE => 'Entreprise',
        };
    }
}
