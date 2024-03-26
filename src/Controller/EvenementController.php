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

}
