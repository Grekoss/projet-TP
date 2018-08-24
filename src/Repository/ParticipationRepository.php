<?php

namespace App\Repository;

use App\Entity\Participation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Participation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participation[]    findAll()
 * @method Participation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Participation::class);
    }

//    /**
//     * @return Participation[] Returns an array of Participation objects
//     */
    

    public function findOneParticipation($event, $user): ?Participation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.event = :val')
            ->setParameter('val', $event)
            ->andWhere('p.participant = :val2')
            ->setParameter('val2', $user)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findParticipationsByEvent($event)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.event = :val')
            ->setParameter('val', $event)
            ->getQuery()
            ->getResult()
        ;
    }

    // query to get an event's participants
    public function getParticipants($event) 
    {
        
        $query = $this->getEntityManager()->createQuery('
            SELECT u 
            FROM App\Entity\User u
            JOIN App\Entity\Participation p
            WHERE u = p.participant
            AND p.event = :event
        ')
        ->setParameter('event', $event);
        
        return $query->getResult(); 
    }

    // SELECT p, u 
            // FROM App\Entity\Participation p
            // JOIN p.participant u
            // WHERE p.event = :event
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Participation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
