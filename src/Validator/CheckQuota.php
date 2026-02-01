<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CheckQuota extends Constraint
{
    public string $message = 'Vous avez atteint votre limite de documents pour ce mois ({{ limit }}/{{ limit }}). Passez au plan Pro pour illimité.';
    
    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
