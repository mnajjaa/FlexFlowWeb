<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\AjouterEvenementType;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File; // Ajoutez cette ligne pour importer la classe File
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use App\Repository\FavorisRepository;
use Symfony\Component\Intl\DateFormat\DateFormat;

class EvenementController extends AbstractController
{

    #[Route("/filtrer-evenements", name: "filtrer_evenements")]

    public function filtrerEvenements(Request $request, EvenementRepository $evenementRepository): Response
    {
        // Récupérer les dates "From" et "To" de la requête
        $fromDateString = $request->query->get('From');
        $toDateString = $request->query->get('To');
        dump($fromDateString, $toDateString);

        // Convertir les dates de l'URL dans le bon format
        $fromDate = date('Y-m-d', strtotime($fromDateString));
        $toDate = date('Y-m-d', strtotime($toDateString));
    
        // Supposons que vous récupériez les événements filtrés de votre repository d'événements
        $evenements = $evenementRepository->filtrerParDate($fromDate, $toDate);
    
        // Convertir les événements filtrés en un tableau associatif pour le rendu dans la vue Twig
        $formattedEvents = [];
        foreach ($evenements as $evenement) {
            $formattedEvents[] = [
                'id' => $evenement->getId(),
                'nomEvenement' => $evenement->getNomEvenement(),
                'categorie' => $evenement->getCategorie(),
                'Objectif' => $evenement->getObjectif(),
                'nbrPlace' => $evenement->getNbrPlace(),
                'Date' => $evenement->getDate()->format('Y-m-d'),
                'Time' => $evenement->getTime()->format('H:i:s'),
                'etat' => $evenement->isEtat() ? 'Actif' : 'Inactif',
                'user' => [
                    'username' => $evenement->getUser()->getUsername(),
                    // Include other user properties if necessary
                ],                // Ajoutez d'autres propriétés d'événement si nécessaire
            ];
        }
    
        return new JsonResponse($formattedEvents);

    }

    
    
    
    
    
 


     #[Route("/admin/ajouterEvenement", name:"ajouter_evenement")]

    public function ajouterEvenement(Request $request): Response
    {
        // Création d'une nouvelle instance de l'entité Evenement
        $evenement = new Evenement();
    
        // Création du formulaire
        $form = $this->createForm(AjouterEvenementType::class, $evenement,
    ['required'=>false]);
    
        // Traitement de la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire est soumis et valide, enregistrez l'événement en base de données
    
            // Gestion de l'upload de l'image
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();
    
            // Vérifier si une image a été téléchargée
            if ($imageFile) {
                // Générer un nom de fichier unique
                $newFilename = file_get_contents($imageFile->getPathname());
    
              
                // Mettre à jour le champ image de l'événement avec le nom du fichier
                $evenement->setImage($newFilename);
            }
    
            // Récupérer l'EntityManager de Doctrine
            $entityManager = $this->getDoctrine()->getManager();
            // Persist et flush l'entité Evenement dans la base de données
            $entityManager->persist($evenement);
            $entityManager->flush();
            return $this->redirectToRoute('evenements_list');
        }
    
        // Afficher le formulaire
        return $this->render('Evenement/AjoutEvenement.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/admin/list', name: 'evenements_list')]
    public function listEvenements(EvenementRepository $EvenementRepository, ReservationRepository $reservationRepo, FavorisRepository $favorisRepo): Response
    {
        // Get statistical data
        $mostReservedEvent = $reservationRepo->findMostReservedEvent();
        $mostLovedEvent = $favorisRepo->findMostLovedEvent();
        $mostHatedEvent = $favorisRepo->findMostUnlovedEvent();
    
        // Create the $data array
        $data = [
            'mostReservedName' => $mostReservedEvent ? $mostReservedEvent['eventName'] : 'No event',
            'mostLovedName' => $mostLovedEvent ? $mostLovedEvent['eventName'] : 'No event',
            'mostHatedName' => $mostHatedEvent ? $mostHatedEvent['eventName'] : 'No event',
            'mostReservedCount' => $mostReservedEvent ? $mostReservedEvent['reservationCount'] : 0,
            'mostLovedCount' => $mostLovedEvent ? $mostLovedEvent['loveCount'] : 0,
            'mostHatedCount' => $mostHatedEvent ? $mostHatedEvent['unloveCount'] : 0,
        ];
    
        // Get events
        $evenements = $EvenementRepository->findAll();
    
        foreach ($evenements as $evenement) {
            // Check if the image exists and convert it to base64 if it does
            if ($evenement->getImage()) {
                $imageData = base64_encode(stream_get_contents($evenement->getImage()));
                $evenement->setImage($imageData);
            }
        }
    
        // Render the template with both statistical data and events
        return $this->render('Evenement/list.html.twig', [
            'data' => $data,
            'evenements' => $evenements,
        ]);
    }

    #[Route('/admin/supprimer/{id}', name: 'Evenement_supprimer', methods: ['POST'])]
    public function supprimerEvenement(Request $request, int $id, EvenementRepository $EvenementRepository): Response
    {
        $evenement = $EvenementRepository->find($id);
    
        if (!$evenement) {
            throw $this->createNotFoundException('l\'evenement avec l\'ID '.$id.' n\'existe pas.');
        }
    
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evenement);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('evenements_list');
    }
    #[Route('/admin/modifier/{id}', name: 'Evenement_modifier')]
    public function modifier(Request $request, int $id, EvenementRepository $EvenementRepository): Response
    {
        // Find the event by its ID
        $evenement = $EvenementRepository->find($id);
    
        // Check if the event exists
        if (!$evenement) {
            throw $this->createNotFoundException('L\'événement avec l\'ID '.$id.' n\'existe pas.');
        }
    
        // Create the form for editing the event
        $form = $this->createForm(AjouterEvenementType::class, $evenement);
    
        // Handle form submission
        $form->handleRequest($request);
    
        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the uploaded image file
            $imageFile = $form->get('imageFile')->getData();
    
            // Check if a new file is uploaded
            if ($imageFile) {
                // Read the content of the file
                $imageContent = file_get_contents($imageFile->getPathname());
    
                // Store the content of the new file in the event entity
                $evenement->setImage($imageContent);
            }
    
            // Update the event in the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
    
          
            
            return $this->redirectToRoute('evenements_list');

                    }
    
        // Render the form for editing the event
        return $this->render('Evenement/modifier.html.twig', [
            'form' => $form->createView(),
            'evenement' => $evenement,

        ]);
    }
    


 
    #[Route("/events", name:"calendar_events")]

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
        return $this->render('Evenement/AdminCalender.html.twig', [
            'data' => $data,
        ]);
    }

  
    #[Route("/api/{id}/edit", name: "api_event_edit")]
    public function majEvent(?Evenement $evenement, Request $request): JsonResponse
    {
        // On récupère les données
        $donnees = json_decode($request->getContent());
    
        if (
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) 
        ) {
            // Les données sont complètes
            // On initialise un code
            $code = 200;
    
            // On vérifie si l'événement existe
            if (!$evenement) {
                return new JsonResponse(['success' => false, 'message' => 'Événement non trouvé.'], 404);
            }
    
            // On met à jour uniquement les champs nécessaires
            $evenement->setNomEvenement($donnees->title);
            $evenement->setDate(new \DateTime($donnees->start));
    
            // Enregistrer les modifications dans la base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
    
            // On retourne le code
            return new JsonResponse(['success' => true, 'message' => 'Événement mis à jour avec succès.']);
        } else {
            // Les données sont incomplètes
            return new JsonResponse(['success' => false, 'message' => 'Données incomplètes.'], 400);
        }
    }
 
}
