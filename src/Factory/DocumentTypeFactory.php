<?php

namespace App\Factory;

use App\Entity\DocumentType;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<DocumentType>
 */
final class DocumentTypeFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return DocumentType::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'slug' => substr(self::faker()->unique()->slug(), 0, 50),
            'name' => substr(self::faker()->words(2, true), 0, 100),
            'description' => self::faker()->sentence(),
            'category' => 'general',
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
