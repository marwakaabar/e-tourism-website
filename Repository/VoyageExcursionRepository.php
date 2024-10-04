<?php

namespace App\Repository;

use App\Entity\VoyageExcursion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VoyageExcursion|null find($id, $lockMode = null, $lockVersion = null)
 * @method VoyageExcursion|null findOneBy(array $criteria, array $orderBy = null)
 * @method VoyageExcursion[]    findAll()
 * @method VoyageExcursion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoyageExcursionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VoyageExcursion::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(VoyageExcursion $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(VoyageExcursion $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return VoyageExcursion[] Returns an array of VoyageExcursion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VoyageExcursion
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
