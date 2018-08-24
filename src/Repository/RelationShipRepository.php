<?php

namespace App\Repository;

use App\Entity\RelationShip;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Repository\LinkRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RelationShip|null find($id, $lockMode = null, $lockVersion = null)
 * @method RelationShip|null findOneBy(array $criteria, array $orderBy = null)
 * @method RelationShip[]    findAll()
 * @method RelationShip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelationShipRepository extends ServiceEntityRepository
{

    private $linkRepo;
    public function __construct(RegistryInterface $registry,LinkRepository $linkRepo)
    {
        $this->linkRepo =$linkRepo;
        parent::__construct($registry, RelationShip::class);
    }

//    /**
//     * @return RelationShip[] Returns an array of RelationShip objects
//     */
    /* */

     // function that gets a user's friends'list
    public function findFriendsByUser(User $user) 
    {
        $friend = $this->linkRepo->getFriendLink();
        return $this->createQueryBuilder('r')    
        ->andWhere('r.userMain = :user')
        ->setParameter('user', $user)
        ->andWhere('r.link = :friend')
        ->setParameter('friend', $friend)
        ->getQuery()
        ->getResult();
    }

    //function to get a relation between two users
    public function findRelation(User $userMain, User $userConcerned)
    {
        return $this->createQueryBuilder('r')
        ->andWhere('r.userMain = :user1')
        ->setParameter('user1', $userMain) 
        ->andWhere('r.userConcerned = :user2')
        ->setParameter('user2', $userConcerned)
        ->getQuery()
        ->getOneOrNullResult()
    ;
    }

    public function findBlacklistByUser(User $user, LinkRepository $repo) 
    {
        $black = $repo->getBlackListLink();
        return $this->createQueryBuilder('r')        
        ->andWhere('r.userMain = :user')
        ->setParameter('user', $user)
        ->andWhere('r.link = :black')
        ->setParameter('black', $black)
        ->getQuery()
        ->getResult();
    }

    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RelationShip
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
