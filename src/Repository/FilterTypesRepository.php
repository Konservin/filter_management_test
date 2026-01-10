<?php

namespace App\Repository;

use App\Entity\FilterTypes;
use App\Entity\TypesSubtypesAssoc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FilterTypes>
 */
class FilterTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FilterTypes::class);
    }

    /**
     * @return FilterTypes[] Returns an array of FilterTypes objects
     */
    public function findByType($value): array
    {
        return $this->createQueryBuilder('type')
            ->innerJoin('type.subType', 'subType', 'WITH', 'type.subType = :value')
            ->andWhere('type.subType = :val')
            ->setParameter('val', $value)
            ->orderBy('type.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findSubtypesByTypeId(int $typeId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('fs')  // Select subtypes
            ->from(TypesSubtypesAssoc::class, 'tsa')  // Start from association table
            ->innerJoin('tsa.subtype', 'fs')  // Join subtypes
            ->innerJoin('tsa.type', 'ft')  // Join filter types
            ->where('ft.id = :typeId')
            ->setParameter('typeId', $typeId);
        return $qb->getQuery()->getResult();
    }
}
