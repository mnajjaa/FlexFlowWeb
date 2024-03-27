<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\AjouterEvenementType;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
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
#[Route("/admin/calendrier", name:"calendrier_evenements")]
public function calendrier(EvenementRepository $evenementRepository): Response
{
    // Récupérer tous les événements depuis le repository
    $evenements = $evenementRepository->findAll();

    // Créer un tableau pour stocker les événements dans un format compréhensible par FullCalendar
    $eventsArray = [];

    foreach ($evenements as $evenement) {
        // Convertir les dates en chaînes de caractères au format ISO 8601
        $start = $evenement->getDate()->format('Y-m-d');
        $end = $evenement->getDate()->format('Y-m-d');

        // Ajouter l'événement au tableau
        $eventsArray[] = [
            'title' => $evenement->getNomEvenement(),
            'start' => $start,
            'end' => $end,
            'id' => $evenement->getId(),
        ];
    }

    // Convertir le tableau en JSON pour l'afficher dans FullCalendar
    $eventsJson = json_encode($eventsArray);

    // Rendre la vue en passant les événements au format JSON
    return $this->render('Evenement/AdminCalender.html.twig', [
        'eventsJson' => $eventsJson,
    ]);
}

#[Route("/admin/modifier-date-evenement/{id}", name:"modifier_date_evenement")]
public function modifierDateEvenement(Request $request, int $id, EvenementRepository $evenementRepository): JsonResponse
{
    // Récupérer l'événement depuis le repository
    $evenement = $evenementRepository->find($id);

    // Vérifier si l'événement existe
    if (!$evenement) {
        return new JsonResponse(['success' => false, 'message' => 'Événement non trouvé.'], 404);
    }

    // Récupérer la nouvelle date depuis la requête
    $newDate = new \DateTime($request->request->get('newDate'));

    // Mettre à jour la date de l'événement
    $evenement->setDate($newDate);

    // Enregistrer les modifications dans la base de données
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->flush();

    // Retourner une réponse JSON
    return new JsonResponse(['success' => true, 'message' => 'Date de l\'événement mise à jour avec succès.']);
}

#[Route("/admin/supprimer-evenement/{id}", name:"supprimer_evenement")]
public function supprimerEvenementClender(Request $request, int $id, EvenementRepository $evenementRepository): JsonResponse
{
    // Récupérer l'événement depuis le repository
    $evenement = $evenementRepository->find($id);

    // Vérifier si l'événement existe
    if (!$evenement) {
        return new JsonResponse(['success' => false, 'message' => 'Événement non trouvé.'], 404);
    }

    // Supprimer l'événement de la base de données
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($evenement);
    $entityManager->flush();

    // Retourner une réponse JSON
    return new JsonResponse(['success' => true, 'message' => 'Événement supprimé avec succès.']);
}


 
    #[Route("/events", name:"calendar_events")]

    public function events(EvenementRepository $eventRepository)
    {
        // Récupérez les événements depuis la base de données
        $events = $eventRepository->findAll(); // C'est un exemple, adaptez cette méthode en fonction de votre logique d'application
        
        // Convertissez les événements en un tableau JSON
        $rdvs = [];
        foreach ($events as $event) {
            $rdvs[] = [
                'title' => $event->getNomEvenement(),
                'start' => $event->getdate()->format('Y-m-d'),
                // Ajoutez d'autres propriétés d'événement si nécessaire
            ];
        }
        
        // Renvoyez les événements au format JSON
        $data = json_encode($rdvs);
        return $this->render('Evenement/AdminCalender.html.twig', [
            'data' => $data,
        ]);

    }
}
