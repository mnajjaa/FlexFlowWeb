<?php

namespace App\Controller;
use App\Controller\SmsController;

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
use Endroid\QrCode\QrCode;
use Endroid\QrCodeBundle\Response\QrCodeResponse;

use Endroid\QrCode\Writer\PngWriter;

class EvenementMemberController extends AbstractController
{


   
    #[Route('/evenement/member', name: 'app_evenement_member')]
    public function index(): Response
    {

        return $this->render('evenement_member/index.html.twig', [
            'controller_name' => 'EvenementMemberController',
        ]);
    }
    #[Route('/qr-code/{id}', name: 'qr_code')]

    public function generateQrCode($id)
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->find($id);
        $participant = $this->getUser(); // Get the current user
    
        // Generate the QR code content
        $content = sprintf(
            'Nom participant: %s, Nom evenement: %s, Date evenement: %s, Date reservation: %s',
            $participant->getUsername(),
            $evenement->getNomEvenement(),
            $evenement->getDate()->format('Y-m-d H:i:s'),
            (new \DateTime())->format('Y-m-d H:i:s')
        );
    
        // Create a QR code
        $qrCode = QrCode::create($content);
    
        // Create a writer
        $writer = new PngWriter();
    
        // Generate a result
        $result = $writer->write($qrCode);
    
        // Create a response
        $response = new QrCodeResponse($result);
    
        return $response;
    }
   

    #[Route('/evenements/{id}', name: 'voir_evenements')]
    public function voirCours(int $id, EvenementRepository $EvenementRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le cours depuis le référentiel en fonction de l'ID
        //$cours = $coursRepository->find($id);
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    
        $evenements = $this->getDoctrine()->getRepository(Evenement::class)->find($id);
    
        $randomEvents = $EvenementRepository->findRandomEventsForUser($user);

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
            'randomEvents' => $randomEvents,


        ]);
    }
    
    
    #[Route('/evenements', name: 'liste_evenement')]
public function listeEvenement(Request $request, EvenementRepository $EvenementRepository): Response
{
   
    $evenements = $EvenementRepository->createQueryBuilder('e')
    ->where('e.Date >= :now')
    ->andWhere('e.etat = :etat')
    ->andWhere('e.nbrPlace > 0') // Add this line
    ->setParameter('now', new \DateTime())
    ->setParameter('etat', 1)
    ->orderBy('e.Date', 'ASC')
    ->getQuery()
    ->getResult();    foreach ($evenements as $evenement) {
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


#[Route('/Evenement/{id}/reserver', name: 'reserver_evenement')]
public function participerEvenement(int $id, EvenementRepository $EvenementRepository, Request $request, EntityManagerInterface $entityManager, SmsController $smsController): Response
{
    // Get the current user's email from the session
    $email = $request->getSession()->get(Security::LAST_USERNAME);

    // Find the user entity based on the email
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

    // Find the course based on the ID
    $evenements = $EvenementRepository->find($id);

    // Check if the user has already participated in this course
    $existingParticipation = $entityManager->getRepository(Reservation::class)->findOneBy([
        'user' => $user,
        'nomEvenement' => $evenements->getNomEvenement()
    ]);

     // Ajoutez une variable pour indiquer si le membre a déjà participé
     $dejaParticipe = ($existingParticipation !== null);

    // If the user has already participated, redirect with an error message
    if ($existingParticipation) {
        return $this->redirectToRoute('liste_evenement');
    }

    // If the course still has capacity
    if ($evenements->getNbrPlace() > 0) {
        // Decrement the course capacity by 1
        $evenements->setNbrPlace($evenements->getNbrPlace() - 1);

        // Create a new Participation entity
        $participation = new Reservation();
        $participation->setNomEvenement($evenements->getNomEvenement());
        $participation->setNomParticipant($user->getNom());
        $participation->setUser($user);
        $participation->setDateReservation(new \DateTime()); // Set the current date

        // Persist the participation and update the course
        $entityManager->persist($participation);
        $entityManager->flush();

         // Ajout d'un message flash de succès
         $this->addFlash('success', 'Réservation succès.');
         

       
         $smsController->sendSMS($participation->getId(), $entityManager);


        // Redirect to the confirmation page
        return $this->redirectToRoute('liste_evenement');
    } 
}
#[Route('/random-events', name: 'random_events')]

public function randomEvents(EvenementRepository $evenementRepository): Response
{
    $user = $this->getUser();
    $randomEvents = $evenementRepository->findRandomEventsForUser($user);

    return $this->render('evenement_member/voir-plus.html.twig', [
        'randomEvents' => $randomEvents,
    ]);

}
}
