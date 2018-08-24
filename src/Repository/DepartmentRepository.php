<?php

namespace App\Repository;

use App\Entity\Department;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Department|null find($id, $lockMode = null, $lockVersion = null)
 * @method Department|null findOneBy(array $criteria, array $orderBy = null)
 * @method Department[]    findAll()
 * @method Department[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepartmentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Department::class);
    }

    public function orderByDepartmentNumber()
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.number', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Department[] Returns an array of Department object where search about the name
     */
    public function findDepartmentByName($number)
    {
        return $this->createQueryBuilder('d')
                    ->where('d.number = :number')
                    ->setParameter('number', $number)
                    ->getQuery()
                    ->getResult()
                    ;
    }
//    /**
//     * @return Department[] Returns an array of Department objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Department
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
