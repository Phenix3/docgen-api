<?php

namespace App\Factory;

use App\Entity\UsageQuota;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<UsageQuota>
 */
final class UsageQuotaFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return UsageQuota::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'monthYear' => self::faker()->dateTime()->format('Y-m'),
            'documentsGenerated' => self::faker()->numberBetween(0, 20),
            'user' => UserFactory::new(),
        ];
    }
}
