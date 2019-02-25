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

    /**
     * @return userReporting[] Returns an array of UserReporting Object for the all user reporting no manage
     */
    public function adminAllUsersReporting()
    {
        return $this->createQueryBuilder('u')
            ->orderby('u.dateAt', 'DESC')
            ->where('u.isManage = 0')
            ->getQuery()
            ->getResult();
    }
}
