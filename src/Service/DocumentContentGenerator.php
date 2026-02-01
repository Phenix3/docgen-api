<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\Template;

class DocumentContentGenerator
{
    public function generate(Document $document): string
    {
        $template = $document->getTemplate();
        $data = $document->getInputData();
        $structure = json_decode($template->getContentStructure(), true);

        if (!is_array($structure) || !isset($structure['sections'])) {
            // Fallback if structure is not valid JSON or doesn't have sections
            return $this->replacePlaceholders($template->getContentStructure(), $data);
        }

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body { font-family: sans-serif; }</style></head><body>';
        $html .= '<h1>' . htmlspecialchars($document->getTemplate()->getTitle()) . '</h1>';

        foreach ($structure['sections'] as $section) {
            if (isset($section['title'])) {
                $html .= '<h2>' . htmlspecialchars($this->replacePlaceholders($section['title'], $data)) . '</h2>';
            }
            if (isset($section['content'])) {
                $html .= '<div>' . nl2br($this->replacePlaceholders($section['content'], $data)) . '</div>';
            }
        }

        $html .= '</body></html>';

        return $html;
    }

    private function replacePlaceholders(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $content = str_replace('{{ ' . $key . ' }}', (string)$value, $content);
                $content = str_replace('{{' . $key . '}}', (string)$value, $content);
            }
        }
        return $content;
    }
}
