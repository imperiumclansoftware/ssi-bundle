<?php

namespace ICS\SsiBundle\Repository;

/**
 * File of Log entity repository
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ICS\SsiBundle\Entity\Log;

/**
 * Log entity repository
 *
 * @package SsiBundle\Repository
 *
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }
}
