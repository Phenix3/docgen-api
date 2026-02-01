<?php

namespace App\Factory;

use App\Entity\Template;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Template>
 */
final class TemplateFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Template::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->words(3, true),
            'description' => self::faker()->sentence(),
            'contentStructure' => json_encode([
                'sections' => [
                    [
                        'title' => 'Introduction',
                        'content' => self::faker()->paragraph()
                    ],
                    [
                        'title' => 'Main Body',
                        'content' => self::faker()->paragraph()
                    ]
                ]
            ]),
            'documentType' => DocumentTypeFactory::new(),
            'country' => CountryFactory::new(),
        ];
    }
}
