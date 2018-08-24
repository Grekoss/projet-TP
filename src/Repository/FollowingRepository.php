<?php

namespace App\Repository;

use App\Entity\Following;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Following|null find($id, $lockMode = null, $lockVersion = null)
 * @method Following|null findOneBy(array $criteria, array $orderBy = null)
 * @method Following[]    findAll()
 * @method Following[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FollowingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Following::class);
    }


    public function getFollowedEvents($user)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT e 
            FROM App\Entity\Event e
            JOIN App\Entity\Following f
            WHERE e = f.event
            AND f.user = :user
        ')
        ->setParameter('user', $user);
        return $query->getResult(); 
    }

    public function findOneFollowing(Event $event, User $user)
    {
        return $this->createQueryBuilder('f')
            ->where('f.event = :val')
            ->setParameter('val', $event)
            ->andWhere('f.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return Following[] Returns an array of Following objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Following
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
