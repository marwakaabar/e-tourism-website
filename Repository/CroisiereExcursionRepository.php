<?php

namespace App\Repository;

use App\Entity\CroisiereExcursion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CroisiereExcursion|null find($id, $lockMode = null, $lockVersion = null)
 * @method CroisiereExcursion|null findOneBy(array $criteria, array $orderBy = null)
 * @method CroisiereExcursion[]    findAll()
 * @method CroisiereExcursion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CroisiereExcursionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CroisiereExcursion::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CroisiereExcursion $entity, bool $flush = true): void
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
    public function remove(CroisiereExcursion $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

     /**
     * @return CroisiereExcursion[] Returns an array of CroisiereExcursion objects
      */
    
  
     
    
    

    /*
    public function findOneBySomeField($value): ?CroisiereExcursion
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
