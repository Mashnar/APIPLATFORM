<?php

namespace App\Repository;

use App\Entity\CheeseListening;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CheeseListening|null find($id, $lockMode = null, $lockVersion = null)
 * @method CheeseListening|null findOneBy(array $criteria, array $orderBy = null)
 * @method CheeseListening[]    findAll()
 * @method CheeseListening[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CheeseListeningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CheeseListening::class);
    }

    // /**
    //  * @return CheeseListening[] Returns an array of CheeseListening objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CheeseListening
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
