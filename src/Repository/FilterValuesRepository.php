<?php
namespace App\Repository;

use App\Entity\FilterValues;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FilterValuesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FilterValues::class);
    }

    public function findValueTypeByTypeId(int $typeId): ?string
    {
        $queryBuilder = $this->createQueryBuilder('fv')
            ->select('fv.valueType') // Map to entity field
            ->where('fv.type = :typeId')
            ->setParameter('typeId', $typeId)
            ->getQuery();

        return $queryBuilder->getSingleScalarResult();
    }
}
