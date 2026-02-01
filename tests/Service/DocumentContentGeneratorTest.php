<?php

namespace App\Tests\Service;

use App\Entity\Document;
use App\Entity\Template;
use App\Service\DocumentContentGenerator;
use PHPUnit\Framework\TestCase;

class DocumentContentGeneratorTest extends TestCase
{
    public function testGenerateReplacesPlaceholders()
    {
        $generator = new DocumentContentGenerator();

        $template = new Template();
        $template->setTitle('Test Document');
        $template->setContentStructure(json_encode([
            'sections' => [
                [
                    'title' => 'Hello {{ name }}',
                    'content' => 'Your score is {{ score }}'
                ]
            ]
        ]));

        $document = new Document();
        $document->setTemplate($template);
        $document->setInputData([
            'name' => 'John',
            'score' => 100
        ]);

        $html = $generator->generate($document);

        $this->assertStringContainsString('<h1>Test Document</h1>', $html);
        $this->assertStringContainsString('<h2>Hello John</h2>', $html);
        $this->assertStringContainsString('<div>Your score is 100</div>', $html);
    }

    public function testGenerateHandlesInvalidJson()
    {
        $generator = new DocumentContentGenerator();

        $template = new Template();
        $template->setTitle('Simple Template');
        $template->setContentStructure('Simple content for {{ name }}');

        $document = new Document();
        $document->setTemplate($template);
        $document->setInputData(['name' => 'Jane']);

        $html = $generator->generate($document);

        $this->assertEquals('Simple content for Jane', $html);
    }
}
