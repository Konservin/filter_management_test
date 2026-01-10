<?php

namespace App\Repository;

use App\Entity\FilterSubtypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypesSubtypesAssoc>
 */
class TypesSubtypesAssocRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FilterSubtypes::class);
    }

}
