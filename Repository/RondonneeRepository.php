<?php

namespace App\Repository;

use App\Entity\Rondonnee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rondonnee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rondonnee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rondonnee[]    findAll()
 * @method Rondonnee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RondonneeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rondonnee::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Rondonnee $entity, bool $flush = true): void
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
    public function remove(Rondonnee $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
      * @return Rondonnee[] Returns an array of Rondonnee objects
      */
    
      public function findByVoyagePay($value)
      {
          return $this->createQueryBuilder('v')
              ->join('v.pays' ,'p')
              ->andWhere('p.id = :val')
              ->setParameter('val', $value)
              ->orderBy('v.id', 'ASC')
              ->setMaxResults(10)
              ->getQuery()
              ->getResult()
          ;
      }
     

    /*
    public function findOneBySomeField($value): ?Rondonnee
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
