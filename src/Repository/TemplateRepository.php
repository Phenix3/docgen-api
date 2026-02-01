<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Country;
use App\Entity\DocumentType;
use App\Entity\Template;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Template>
 */
class TemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Template::class);
    }

    /**
     * @return Template[]
     */
    public function findByCountryAndType(Country $country, DocumentType $documentType): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.country = :country')
            ->andWhere('t.documentType = :type')
            ->andWhere('t.isActive = :active')
            ->setParameter('country', $country)
            ->setParameter('type', $documentType)
            ->setParameter('active', true)
            ->orderBy('t.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
