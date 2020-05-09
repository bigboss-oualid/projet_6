<?php

namespace App\Repository;

use App\Entity\UserUpdateTrick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserUpdateTrick|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserUpdateTrick|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserUpdateTrick[]    findAll()
 * @method UserUpdateTrick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserUpdateTrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserUpdateTrick::class);
    }

    // /**
    //  * @return UserUpdateTrick[] Returns an array of UserUpdateTrick objects
    //  */
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
    public function findOneBySomeField($value): ?UserUpdateTrick
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
