<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository

{

    private $linkrepo;
    private $vRepo;

    public function __construct(RegistryInterface $registry, LinkRepository $linkrepo, VisibilityRepository $vRepo)
    {
        $this->linkrepo = $linkrepo;
        $this->vRepo = $vRepo;
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[] Returns an array of Event Objet limit 4 for homepage and no banish
     */
    public function showForHomepage()
    {
        $public = $this->vRepo->getPublicVisibility();
        $now = new \DateTime('-1 second');

        return $this->createQueryBuilder('e')
                    ->orderBy('e.dateAt', 'ASC')
                    ->where('e.isActive = 1')
                    ->andWhere('e.visibility = :public')
                    ->andWhere('e.dateAt >= :now')
                    ->setParameter('public', $public)
                    ->setParameter('now', $now)
                    ->setMaxResults(4)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return Event[] Returns an array of Event Object.
     * For a list of all events without the "banned"
     */
    public function findAllEvents()
    {   
        $public = $this->vRepo->getPublicVisibility();
        $now = new \DateTime('-1 second');
       
        return $this->createQueryBuilder('e')
                    ->orderBy('e.dateAt', 'ASC')
                    ->where('e.isActive = 1')
                    ->andWhere('e.visibility = :public')
                    ->andWhere('e.dateAt >= :now')
                    ->setParameter('public', $public)
                    ->setParameter('now', $now)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return Event[] Returns an array of Events Objects
     * A list of all events the user is allowed to see (blacklist, age and private filters set) 
     */
    public function findAllEventsConnected($age, $user)
    {
        $public = $this->vRepo->getPublicVisibility();
        $friend =$this->linkrepo->getFriendLink();
        $private = $this->vRepo->getPrivateVisibility();
        $now = new \DateTime('-1 sec');
      
        return $this->createQueryBuilder('e')
                    ->leftJoin('e.organize', 'o')
                    ->leftJoin('o.mainRelationShips', 'mr')
                    ->where('mr.link = :friend AND mr.userConcerned = :user')
                    ->orWhere('e.visibility = :public AND :age BETWEEN e.minAge AND e.maxAge')
                    ->andWhere('e.isActive = 1')
                    ->andWhere('e.dateAt >= :now')
                    ->orderBy('e.dateAt', 'ASC')
                    ->setParameter('age', $age)
                    ->setParameter('public', $public)
                    ->setParameter('friend', $friend)
                    ->setParameter('user', $user)
                    ->setParameter('now', $now)
                    ->getQuery()
                    ->getResult();
    }

 
    /**
     * @return Event[] Returns an array of Event Object
     * For list of all events where the user participe without "bannish"
     */
    public function findAllEventsbyUser($user)
    {
        $now = new \DateTime('-1 day');
        return $this->createQueryBuilder('e')
            ->join('e.participations', 'p')
            ->where('p.participant = :theUser')
            ->andWhere('e.isActive = 1')
            ->andWhere('e.dateAt >= :now')
            ->orderBy('e.dateAt', 'ASC')
            ->setParameter('theUser', $user)
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[] Returns an array of Event Object
     * For list of all events where the user organize events without "bannish"
     */
    public function findAllEventsOrganizebyUser($user)
    {
        return $this->createQueryBuilder('e')
                    ->where('e.organize = :theUser')
                    ->andWhere('e.isActive = 1')
                    ->orderBy('e.dateAt', 'ASC')
                    ->setParameter('theUser', $user)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return Event[] Returns an array of Event Object
     * A list of all events the user organized between 5 days ago and yesterday
     */
    public function findFiveDaysEventsOrganizebyUser($user)
    {
        $date = new \DateTime('-5 day');
        $yesterday= new \DateTime('-1 day');

        return $this->createQueryBuilder('e')
                    ->where('e.organize = :theUser')
                    ->andWhere('e.isActive = 1')
                    ->andWhere('e.dateAt BETWEEN :date AND :yesterday')
                    ->orderBy('e.dateAt', 'ASC')
                    ->setParameter('theUser', $user)
                    ->getQuery()
                    ->getResult();
    }


    /**
     * @return Event[] Returns an array of Event Object
     * A list of all events that are forbidden for the current user"
     */
    public function findForbiddenEvents($user)
    {
        
        $blacklist =$this->linkrepo->getBlackListLink();
        $now = new \DateTime();
        $query = $this->getEntityManager()->createQuery('
        SELECT e 
            FROM App\Entity\Event e
            JOIN App\Entity\RelationShip r
            WHERE e.organize = r.userMain
            AND r.link = :blacklist
            AND r.userConcerned = :user
            AND e.dateAt >= :now
            
       ')
        ->setParameter('blacklist', $blacklist)
        ->setParameter('user', $user)
        ->setParameter('now', $now);
        return $query->getResult();
    }

     /**
     * @return Event[] Return an array of Event Object in which the current user is invited in
     */
    public function findEventsWhereInvited($user)
    {
        $friend =$this->linkrepo->getFriendLink();
        $query = $this->getEntityManager()->createQuery('
        SELECT e 
            FROM App\Entity\Event e
            JOIN App\Entity\RelationShip r
            WHERE e.organize = r.userMain
            AND r.link = :friend
            AND r.userConcerned = :user
       ')
        ->setParameter('friend', $friend)
        ->setParameter('user', $user);

        return $query->getResult();
    }
    
    /**
     * @return Event[] Return an array of Event Object for have the list events by Departments and not bannish
     */
    public function findEventsbyDepartment($department)
    {
        return $this->createQueryBuilder('e')
                    ->where('e.department = :department')
                    ->andWhere('e.isActive = 1')
                    ->setParameter('department', $department)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return Event[] Return an array of Event Object for have the list events by Region and not bannish
     */
    public function findEventsbyRegion($region)
    {
        return $this->createQueryBuilder('e')
                    ->join('e.department', 'd')
                    ->join('d.region', 'r')
                    ->where('r.id = :region')
                    ->andWhere('e.isActive = 1')
                    ->setParameter('region', $region)
                    ->getQuery()
                    ->getResult();
    }

    /** 
     * @return Event[] Return an array of Event Object for have the list events by Tag and not bannish
     */
    public function findEventsByTag($tag)
    {
        return $this->createQueryBuilder('e')
                    ->join('e.tags', 't')
                    ->where('t.id = :tag')
                    ->andWhere('e.isActive = 1')
                    ->setParameter('tag', $tag)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return Event[] Return an array of Event Object for the bar Search in name or description without bannish
     */
    public function findEventsWithBarSearch($word)
    {
        return $this->createQueryBuilder('e')
                    ->where('e.name LIKE :search OR e.description LIKE :search')
                    ->andWhere('e.isActive = 1')
                    ->setParameter('search', '%'.$word.'%')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return Event[] Return an array of Event Object for search by Tags(array)
     */
    public function findCustomEventByTags($tagsSelect)
    {
        return $this->createQueryBuilder('e')
            ->join('e.tags', 't')
            ->where('t.id IN (:tagsSelect)')
            ->setParameter('tagsSelect', $tagsSelect)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[] Return an array of Event Object for look the events when the MainUser is friend with the other users
     */

    public function findEventByFriend($user)
    {
        return $this->createQueryBuilder('e')
            ->join('e.participations', 'p')
            ->join('p.participant', 'u')
            ->join('u.userConcernedRelationShips', 'r')
            ->where('r.link = 1')
            ->andWhere('r.userMain = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[] Return an array of Event Object for the big search custom
     * All if = false or an object for the requete is done, it is the "SearchController::search" where is defined
     */
    public function searchCustom($dateStart, $dateEnd, $rating, $department,  $region, $selectTags, $friend, $userInterface )
    {
        $query = $this->createQueryBuilder('e')
            ->join('e.organize', 'u')
            ->where('e.dateAt BETWEEN :dateStart AND :dateEnd')
            ->andwhere('u.rating BETWEEN :rating AND 5')
            ->andWhere('e.isActive = 1')
            ->setParameter('dateStart', $dateStart->format('Y-m-d'))
            ->setParameter('dateEnd', $dateEnd->format('Y-m-d'))
            ->setParameter('rating', $rating);

        if ($department) {
            $query ->andWhere('e.department IN (:department)')
                ->setParameter('department', $department);
        }
 
        if ($region) {
            $query->join('e.department', 'd')
                ->join('d.region', 'r')
                ->andWhere('r.id IN (:region)')
                ->setParameter('region', $region);
        }

        if ($selectTags) {
            $query->join('e.tags', 't')
                ->andWhere('t.id IN (:selectTags)')
                ->setParameter('selectTags', $selectTags);
        }

        if ($friend) {
            $query->join('e.participations', 'p')
                ->join('p.participant', 'us')
                ->join('us.userConcernedRelationShips', 're')
                ->andWhere('re.link = 1')
                ->andWhere('re.userMain = :userInterface')
                ->setParameter('userInterface', $userInterface);
        } 

        return $query->getQuery()->getResult();
    }
}
