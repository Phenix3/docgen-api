<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UsageQuota;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UsageQuota>
 */
class UsageQuotaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsageQuota::class);
    }

    public function findCurrentQuota(User $user): ?UsageQuota
    {
        $currentMonth = (new \DateTimeImmutable())->format('Y-m');

        return $this->createQueryBuilder('uq')
            ->andWhere('uq.user = :user')
            ->andWhere('uq.monthYear = :monthYear')
            ->setParameter('user', $user)
            ->setParameter('monthYear', $currentMonth)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
