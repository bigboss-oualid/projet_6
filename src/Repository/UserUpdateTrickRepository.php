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
}
