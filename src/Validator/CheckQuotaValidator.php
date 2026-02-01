<?php

namespace App\Validator;

use App\Entity\Document;
use App\Entity\User;
use App\Service\QuotaService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CheckQuotaValidator extends ConstraintValidator
{
    public function __construct(
        private readonly QuotaService $quotaService,
        private readonly Security $security,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CheckQuota) {
            throw new UnexpectedTypeException($constraint, CheckQuota::class);
        }

        // Only validate on Document creation
        if (!$value instanceof Document) {
            return;
        }

        $user = $this->security->getUser();

        // If user is not logged in (should not happen due to firewall), skip or fail.
        // Assuming firewall protects this resource.
        if (!$user instanceof User) {
            return;
        }

        if (!$this->quotaService->checkQuota($user)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ limit }}', (string) $user->getSubscriptionType()->monthlyLimit())
                ->addViolation();
        }
    }
}
