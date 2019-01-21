<?php

namespace App\Repository;

use App\Entity\AWSObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AWSObject|null find($id, $lockMode = null, $lockVersion = null)
 * @method AWSObject|null findOneBy(array $criteria, array $orderBy = null)
 * @method AWSObject[]    findAll()
 * @method AWSObject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AWSObjectRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AWSObject::class);
    }

    // /**
    //  * @return AWSObject[] Returns an array of AWSObject objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AWSObject
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
