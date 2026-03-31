<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Driver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Driver>
 */
class DriverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Driver::class);
    }

    public function findByCompany(Company $company): array
    {
        return $this->createQueryBuilder('d')
            ->join('d.companyDepot', 'cd')
            ->andWhere('d.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult();
    }
}
