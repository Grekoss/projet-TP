<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{

    private $linkRepo;
    public function __construct(RegistryInterface $registry, LinkRepository $linkRepo)
    {
        $this->linkRepo=$linkRepo;
        parent::__construct($registry, User::class);
    }

    // On utilise une fonction maison pour qu'àla connexion l'email ou le pseudo soient reconnus
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // récupération des hommes
    public function findUserByGenreName($genre)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.genre', 'g')
            ->addselect('g')
            ->where('g.name = :genre')
            ->setParameter('genre', $genre)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return user[] Returns an array of User objects for have the six last users created and not bannish
     */
    public function findSixLastUsersCreated()
    {
        return $this->createQueryBuilder('u')
                    ->orderby('u.createdAt', 'DESC')
                    ->where('u.isActive = 1')
                    ->setMaxResults(6)
                    ->getQuery()
                    ->getResult()
        ;
    }

    /**
     * @return user[] Returns an array of User objects for have the six last users connected and not bannish
     */
    public function findSixLastUsersConnected()
    {
        return $this->createQueryBuilder('u')
                    ->orderby('u.connectedAt', 'DESC')
                    ->where('u.isActive = 1')
                    ->setMaxResults(6)
                    ->getQuery()
                    ->getResult()
        ;
    }

    /**
     * @return user[] Returns an array of User object for have the list users by Departments and not bannish
     */
    public function findUsersbyDepartement($department)
    {
        return $this->createQueryBuilder('u')
            ->where('u.department = :department')
            ->andWhere('u.isActive = 1')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return user[] Return an array of User object for have the list users by Region and not bannish
     */
    public function findUsersbyRegion($region)
    {
        return $this->createQueryBuilder('u')
            ->join('u.department', 'd')
            ->join('d.region', 'r')
            ->where('r.id = :region')
            ->andWhere('u.isActive = 1')
            ->setParameter('region', $region)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return user[] Returns an array of User object for have the all members and not bannish and order by username
     */
    public function findAllUsersbyUsername()
    {
        return $this->createQueryBuilder('u')
                    ->where('u.isActive = 1')
                    ->orderBy('u.username', 'ASC')
                    ->getQuery()
                    ->getResult()
                    ;
    }
    /**
     * @return user[] Returns an array of User Object for the news user (3 months)
     */
    public function newsUser()
    {
        return $this->createQueryBuilder('u')
            ->where('u.createdAt BETWEEN :nowBefore AND :now')
            ->setParameter('now', new \DateTime)
            ->setParameter('nowBefore', new \DateTime('- 90 day'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return user[] Returns an array of User Object for the big search custom
     * All if = false or an object for the requete is done, it is the "SearchController::search" where is defined
     */
    public function searchCustom($new, $rating, $ageStart, $ageEnd, $genre, $department, $region)
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.rating BETWEEN :rating AND 5')
            ->andWhere('u.birthDate BETWEEN :ageEnd AND :ageStart')
            ->andWhere('u.isActive = 1')
            ->setParameter('rating', $rating)
            ->setParameter('ageStart', $ageStart)
            ->setParameter('ageEnd', $ageEnd);

        if ($new) {
            $query->andWhere('u.createdAt BETWEEN :nowBefore AND :now')
                ->setParameter('nowBefore', new \DateTime('- 90 day'))
                ->setParameter('now', new \DateTime);
        }

        if ($genre) {
            $query->join('u.genre', 'g')
                ->andWhere('g.id = :genre')
                ->setParameter('genre', $genre);
        }

        if ($department) {
            $query->andWhere('u.department IN (:department)')
                ->setParameter('department', $department);
        }

        if ($region) {
            $query->join('u.department', 'd')
                ->join('d.region', 'r')
                ->andWhere('r.id IN (:region)')
                ->setParameter('region', $region);
        }

        return $query->getQuery()->getResult();

    }

    public function findFriends($user)
    {
        $friend =$this->linkRepo->getFriendLink();
        $query = $this->getEntityManager()->createQuery('
            SELECT u 
            FROM App\Entity\User u
            JOIN App\Entity\RelationShip r
            WHERE u = r.userConcerned
            AND r.link = :friend
            AND r.userMain = :user
       ')
       ->setParameter('friend', $friend)
       ->setParameter('user', $user);
       return $query->getResult();
    }


//    /**
//     * @return User[] Returns an array of User objects
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

    /*
    public function findOneBySomeField($value): ?User
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
