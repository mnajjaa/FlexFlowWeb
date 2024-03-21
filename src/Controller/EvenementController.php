<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\AjouterEvenementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
    
                // Déplacer le fichier téléchargé vers le répertoire souhaité
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur si le déplacement du fichier échoue
                }
    
                // Mettre à jour le champ image de l'événement avec le nom du fichier
                $evenement->setImage($newFilename);
            }
    
            // Récupérer l'EntityManager de Doctrine
            $entityManager = $this->getDoctrine()->getManager();
            // Persist et flush l'entité Evenement dans la base de données
            $entityManager->persist($evenement);
            $entityManager->flush();
    
            // Rediriger vers une page de confirmation ou une autre page après l'ajout de l'événement
            return $this->redirectToRoute('ajouter_evenement');
        }
    
        // Afficher le formulaire
        return $this->render('Evenement/AjoutEvenement.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
