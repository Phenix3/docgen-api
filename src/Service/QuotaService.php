<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UsageQuota;
use App\Entity\User;
use App\Repository\UsageQuotaRepository;
use Doctrine\ORM\EntityManagerInterface;

class QuotaService
{
    public function __construct(
        private readonly UsageQuotaRepository $quotaRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getQuotaForUser(User $user): UsageQuota
    {
        $month = (new \DateTimeImmutable())->format('Y-m');
        $quota = $this->quotaRepository->findOneBy([
            'user' => $user,
            'monthYear' => $month,
        ]);

        if (!$quota) {
            $quota = new UsageQuota();
            $quota->setUser($user);
            $quota->setMonthYear($month);
            $quota->setDocumentsGenerated(0);
            $quota->setDocxExports(0);
            
            $this->entityManager->persist($quota);
            // We don't flush here to allow the caller to flush when needed, 
            // or we flush if it's a "get or create" safe method.
            // For now, let's flush to ensure consistency if it doesn't exist.
            $this->entityManager->flush();
        }

        return $quota;
    }

    public function checkQuota(User $user): bool
    {
        $limit = $user->getSubscriptionType()->monthlyLimit();
        $quota = $this->getQuotaForUser($user);

        return $quota->getDocumentsGenerated() < $limit;
    }

    public function incrementQuota(User $user): void
    {
        $quota = $this->getQuotaForUser($user);
        $quota->setDocumentsGenerated($quota->getDocumentsGenerated() + 1);
        
        $this->entityManager->flush();
    }
}
