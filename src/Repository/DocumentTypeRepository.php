<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DocumentType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentType>
 */
class DocumentTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentType::class);
    }

    /**
     * @return DocumentType[]
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('dt')
            ->andWhere('dt.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('dt.sortOrder', 'ASC')
            ->addOrderBy('dt.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBySlug(string $slug): ?DocumentType
    {
        return $this->createQueryBuilder('dt')
            ->andWhere('dt.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return DocumentType[]
     */
    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('dt')
            ->andWhere('dt.category = :category')
            ->andWhere('dt.isActive = :active')
            ->setParameter('category', $category)
            ->setParameter('active', true)
            ->orderBy('dt.sortOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
