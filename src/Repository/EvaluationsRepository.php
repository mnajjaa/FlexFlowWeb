<?php

namespace App\Repository;

use App\Entity\Evaluations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evaluations>
 *
 * @method Evaluations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evaluations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evaluations[]    findAll()
 * @method Evaluations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluations::class);
    }

//    /**
//     * @return Evaluations[] Returns an array of Evaluations objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Evaluations
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
