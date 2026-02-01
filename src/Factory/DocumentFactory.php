<?php

namespace App\Factory;

use App\Entity\Document;
use App\Enum\DocumentStatus;
use App\Enum\ToneLevel;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Document>
 */
final class DocumentFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Document::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'generatedContent' => self::faker()->paragraphs(3, true),
            'inputData' => ['name' => self::faker()->name(), 'field' => 'value'],
            'status' => self::faker()->randomElement(DocumentStatus::cases()),
            'tone' => self::faker()->randomElement(ToneLevel::cases()),
            'user' => UserFactory::new(),
            'template' => TemplateFactory::new(),
            'country' => CountryFactory::new(),
        ];
    }
}
