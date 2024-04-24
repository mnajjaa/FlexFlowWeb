<?php

namespace App\Repository;

use App\Entity\Favoris;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favoris>
 *
 * @method Favoris|null find($id, $lockMode = null, $lockVersion = null)
 * @method Favoris|null findOneBy(array $criteria, array $orderBy = null)
 * @method Favoris[]    findAll()
 * @method Favoris[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavorisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favoris::class);
    }

//    /**
//     * @return Favoris[] Returns an array of Favoris objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Favoris
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function findMostLovedEvent()
{
    return $this->createQueryBuilder('f')
        ->select('IDENTITY(f.evenement) as eventId, e.nomEvenement as eventName, COUNT(f.id) as loveCount')
        ->join('f.evenement', 'e')
        ->where('f.loved = :loved')
        ->setParameter('loved', true)
        ->groupBy('f.evenement')
        ->orderBy('loveCount', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
}

public function findMostUnlovedEvent()
{
    return $this->createQueryBuilder('f')
        ->select('IDENTITY(f.evenement) as eventId, e.nomEvenement as eventName, COUNT(f.id) as unloveCount')
        ->join('f.evenement', 'e')
        ->where('f.unloved = :unloved')
        ->setParameter('unloved', true)
        ->groupBy('f.evenement')
        ->orderBy('unloveCount', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
}
}
