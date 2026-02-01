<?php

declare(strict_types=1);

namespace App\Service\AI;

final class PromptBuilder
{
    /**
     * Construit le prompt final en remplaçant les variables
     *
     * @param string $template Template avec placeholders {variable}
     * @param array<string, mixed> $data Données à injecter
     * @return string Prompt final
     * @throws \InvalidArgumentException Si une variable requise est manquante
     */
    public function build(string $template, array $data): string
    {
        $prompt = $template;
        
        // Remplacer les variables
        foreach ($data as $key => $value) {
            $placeholder = '{' . $key . '}';
            $stringValue = $this->valueToString($value);
            $prompt = str_replace($placeholder, $stringValue, $prompt);
        }

        // Vérifier qu'il ne reste pas de placeholders non remplacés
        if (preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $prompt, $matches)) {
            $missingVars = array_unique($matches[1]);
            throw new \InvalidArgumentException(
                sprintf('Variables manquantes dans le prompt: %s', implode(', ', $missingVars))
            );
        }

        return $prompt;
    }

    /**
     * Valide que les données contiennent tous les champs requis
     *
     * @param array<string, array{required?: bool, type?: string}> $schema
     * @param array<string, mixed> $data
     * @return array<string, string> Erreurs de validation
     */
    public function validate(array $schema, array $data): array
    {
        $errors = [];

        foreach ($schema as $field => $rules) {
            $isRequired = $rules['required'] ?? false;
            $type = $rules['type'] ?? 'string';

            if ($isRequired && (!isset($data[$field]) || $data[$field] === '')) {
                $errors[$field] = sprintf('Le champ "%s" est requis', $field);
                continue;
            }

            if (isset($data[$field]) && $data[$field] !== '') {
                if (!$this->validateType($data[$field], $type)) {
                    $errors[$field] = sprintf('Le champ "%s" doit être de type %s', $field, $type);
                }
            }
        }

        return $errors;
    }

    private function valueToString(mixed $value): string
    {
        if (is_array($value)) {
            return implode(', ', array_map(fn($v) => $this->valueToString($v), $value));
        }
        
        if ($value instanceof \DateTimeInterface) {
            return $value->format('d/m/Y');
        }

        if (is_bool($value)) {
            return $value ? 'Oui' : 'Non';
        }

        return (string) $value;
    }

    private function validateType(mixed $value, string $type): bool
    {
        return match ($type) {
            'string' => is_string($value),
            'int', 'integer' => is_int($value) || (is_string($value) && ctype_digit($value)),
            'email' => is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'date' => $value instanceof \DateTimeInterface || (is_string($value) && strtotime($value) !== false),
            'bool', 'boolean' => is_bool($value),
            'array' => is_array($value),
            default => true,
        };
    }
}
