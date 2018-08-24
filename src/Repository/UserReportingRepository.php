<?php

namespace App\Repository;

use App\Entity\UserReporting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserReporting|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserReporting|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserReporting[]    findAll()
 * @method UserReporting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserReportingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserReporting::class);
    }

    public function findLastUserReport()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.dateAt', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return UserReporting[] Returns an array of UserReporting objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserReporting
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
