<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class EvenementMemberController extends AbstractController
{
    #[Route('/evenement/member', name: 'app_evenement_member')]
    public function index(): Response
    {
        return $this->render('evenement_member/index.html.twig', [
            'controller_name' => 'EvenementMemberController',
        ]);
    }


    #[Route('/evenements/{id}', name: 'voir_evenements')]
    public function voirCours(int $id, EvenementRepository $EvenementRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le cours depuis le référentiel en fonction de l'ID
        //$cours = $coursRepository->find($id);
        $evenements = $this->getDoctrine()->getRepository(Evenement::class)->find($id);
    

        // Vérifier si le cours existe
        if (!$evenements) {
            throw new NotFoundHttpException('evenement non trouvé');
        }
     // Vérifier si l'utilisateur a déjà participé à ce cours
    $email = $request->getSession()->get(Security::LAST_USERNAME);
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    $existingParticipation = $entityManager->getRepository(Reservation::class)->findOneBy([
        'user' => $user,
        'nomEvenement' => $evenements->getNomEvenement()
    ]);

        $evenements->image = base64_encode(stream_get_contents($evenements->getImage()));
        $dejaParticipe = ($existingParticipation !== null);
        // Afficher les détails du cours dans un nouveau template
        return $this->render('evenement_member/voir-plus.html.twig', [
            'evenements' => $evenements,
            'dejaParticipe' => $dejaParticipe,

        ]);
    }
    
    
    #[Route('/evenements', name: 'liste_evenement')]
public function listeEvenement(Request $request, EvenementRepository $EvenementRepository): Response
{
   
    $evenements = $EvenementRepository->findBy(['etat' => 1]);
    foreach ($evenements as $evenement) {
        $evenement->setImage(base64_encode(stream_get_contents($evenement->getImage())));
    }

    return $this->render('evenement_member/voir.html.twig', [
        'evenements' => $evenements,
    ]);
}


#[Route("/events/Member", name:"calendar_member")]

public function events(EvenementRepository $eventRepository)
{
    // Récupérez les événements depuis la base de données
    $events = $eventRepository->findAll(); // C'est un exemple, adaptez cette méthode en fonction de votre logique d'application
    
    // Convertissez les événements en un tableau JSON
    $rdvs = [];
    foreach ($events as $event) {
        // Récupérez la date et l'heure séparément
        $date = $event->getDate()->format('Y-m-d');
        $time = $event->getTime()->format('H:i:s');

        $start = $date . 'T' . $time;

        $rdvs[] = [
            'id'=>$event->getId(),
            'title' => $event->getNomEvenement(),
            'start' => $start,
            'categorie'=>$event->getCategorie(),
            'objectif'=>$event->getObjectif(),
            "nbrdePlace"=>$event->getNbrPlace(),
            'Etat'=>$event->isEtat(),
            'user'=>$event->getUser(),
            

            // Ajoutez d'autres propriétés d'événement si nécessaire
        ];
    }
    
    // Renvoyez les événements au format JSON
    $data = json_encode($rdvs);
    return $this->render('evenement_member/calenderMember.html.twig', [
        'data' => $data,
    ]);
}

}
