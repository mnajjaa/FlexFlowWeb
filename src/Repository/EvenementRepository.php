<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;
use App\Entity\User;
use App\Entity\Reservation;
use App\Entity\Favoris;

use Doctrine\ORM\EntityManagerInterface;


/**
 * @extends ServiceEntityRepository<Evenement>
 *
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{   private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Evenement::class);
        $this->entityManager = $entityManager;
    }

//    /**
//     * @return Evenement[] Returns an array of Evenement objects
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

//    public function findOneBySomeField($value): ?Evenement
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


public function filtrerParDate(string $fromDate, string $toDate): array
{
    $fromDate = \DateTime::createFromFormat('Y-m-d', $fromDate);
    $toDate = \DateTime::createFromFormat('Y-m-d', $toDate);

    $evenements = $this->createQueryBuilder('e')
        ->where('e.Date >= :fromDate')
        ->andWhere('e.Date <= :toDate')
        ->setParameter('fromDate', $fromDate)
        ->setParameter('toDate', $toDate)
        ->getQuery()
        ->getResult();
        dump($evenements);

    return $evenements;
}

public function findRandomEventsForUser(User $user): array
{
    // Get the names of the events the user has booked or liked
    $reservations = $this->entityManager->getRepository(Reservation::class)->findBy(['user' => $user]);
    $nomEvenements = array_map(fn(Reservation $r) => $r->getNomEvenement(), $reservations);
    $favoris = $this->entityManager->getRepository(Favoris::class)->findBy(['user' => $user]);
$favorisEvenements = array_map(fn(Favoris $f) => $f->getEvenement(), $favoris);

    $eventNames = array_merge(
        $nomEvenements,
        $favorisEvenements    );
    // Get the categories of these events
    $categories = $this->createQueryBuilder('e')
        ->select('e.categorie')
        ->where('e.nomEvenement IN (:eventNames)')
        ->andWhere('e.etat = 1')
        ->setParameter('eventNames', $eventNames)
        ->getQuery()
        ->getResult();

    // Get the total number of events that meet the criteria
    $count = $this->createQueryBuilder('e')
        ->where('e.categorie IN (:categories)')
        ->andWhere('e.etat = 1')
        ->setParameter('categories', $categories)
        ->select('COUNT(e)')
        ->getQuery()
        ->getSingleScalarResult();

    // If there are less than 2 events, return all of them
    if ($count < 2) {
        return $this->createQueryBuilder('e')
            ->where('e.categorie IN (:categories)')
            ->andWhere('e.etat = 1')
            ->setParameter('categories', $categories)
            ->getQuery()
            ->getResult();
    }

    // Otherwise, return 2 random events
    $random = rand(0, $count - 2);

    return $this->createQueryBuilder('e')
        ->where('e.categorie IN (:categories)')
        ->andWhere('e.etat = 1')
        ->setParameter('categories', $categories)
        ->setFirstResult($random)
        ->setMaxResults(2)
        ->getQuery()
        ->getResult();
}

}
