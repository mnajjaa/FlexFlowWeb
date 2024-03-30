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

class EvenementController extends AbstractController
{

 



     #[Route("/admin/ajouterEvenement", name:"ajouter_evenement")]

    public function ajouterEvenement(Request $request): Response
    {
        // Création d'une nouvelle instance de l'entité Evenement
        $evenement = new Evenement();
    
        // Création du formulaire
        $form = $this->createForm(AjouterEvenementType::class, $evenement);
    
        // Traitement de la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire est soumis et valide, enregistrez l'événement en base de données
    
            // Gestion de l'upload de l'image
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
    
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
            $this->addFlash('success', 'Evenement a été ajouté avec succès.');

            // Rediriger vers une page de confirmation ou une autre page après l'ajout de l'événement
            return $this->redirectToRoute('evenements_list');
        }
    
        // Afficher le formulaire
        return $this->render('Evenement/AjoutEvenement.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/admin/list', name: 'evenements_list')]
    public function listEvenements(EvenementRepository $EvenementRepository): Response
    {
       

        // Récupérer tous les événements depuis le repository
        $evenements = $EvenementRepository->findAll();

        foreach ($evenements as $evenement) {
            // Vérifier si l'image existe
            if ($evenement->getImage()) {
                // Convertir les données binaires en base64
                $imageData = base64_encode(stream_get_contents($evenement->getImage()));
                $evenement->setImage($imageData);
            }
        }

        // Rendre la vue en passant les événements récupérés
        return $this->render('Evenement/list.html.twig', [
            'evenement' => $evenements,
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
    $evenement = $EvenementRepository->find($id);

    if (!$evenement) {
        throw $this->createNotFoundException('L\'événement avec l\'ID '.$id.' n\'existe pas.');
    }

    // Créer le formulaire de modification
    $form = $this->createForm(AjouterEvenementType::class, $evenement);

    // Gérer la soumission du formulaire
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Si le formulaire est soumis et valide, enregistrer les modifications dans la base de données
        $this->getDoctrine()->getManager()->flush();

        // Rediriger vers la liste des événements
        return $this->redirectToRoute('evenements_list');
    }

    // Afficher le formulaire de modification
    return $this->render('Evenement/modifier.html.twig', [
        'form' => $form->createView(),
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
    #[Route("/filtrer-evenements", name: "filtrer_evenements")]
    public function filtrerEvenements(Request $request, EvenementRepository $evenementRepository): JsonResponse
    {
        // Récupérer les dates "From" et "To" de la requête
        $fromDate = new \DateTime($request->query->get('from'));
        $toDate = new \DateTime($request->query->get('to'));
        
        // Effectuer la logique de filtrage des événements en fonction de ces dates
        // Vous devrez implémenter cette logique en fonction de votre modèle de données et de votre logique métier
        
        // Supposons que vous récupériez les événements filtrés de votre repository d'événements
        $evenements = $evenementRepository->filtrerParDate($fromDate, $toDate);
        
        // Convertir les événements filtrés en un tableau associatif pour la réponse JSON
        $formattedEvents = [];
        foreach ($evenements as $evenement) {
            $formattedEvents[] = [
                'id' => $evenement->getId(),
                'nomEvenement' => $evenement->getNomEvenement(),
                'categorie' => $evenement->getCategorie(),
                'objectif' => $evenement->getObjectif(),
                'nbrPlace' => $evenement->getNbrPlace(),
                'Date' => $evenement->getDate()->format('Y-m-d'),
                'Time' => $evenement->getTime()->format('H:i:s'),
                'etat' => $evenement->isEtat() ? 'Actif' : 'Inactif',
                'user' => $evenement->getUser()->getUsername(),
                // Ajoutez d'autres propriétés d'événement si nécessaire
            ];
        }
        
        // Retourner les événements filtrés au format JSON
        return new JsonResponse($formattedEvents);
    }
    
    
 
}
