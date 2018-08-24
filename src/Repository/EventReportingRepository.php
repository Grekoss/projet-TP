<?php

namespace App\Repository;

use App\Entity\EventReporting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EventReporting|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventReporting|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventReporting[]    findAll()
 * @method EventReporting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventReportingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EventReporting::class);
    }

    public function findLastEventReport()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.dateAt', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
            ;
    }
//    /**
//     * @return EventReporting[] Returns an array of EventReporting objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EventReporting
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
