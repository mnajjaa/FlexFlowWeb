<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\CoursRepository;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Repository\RatingRepository;
use App\Repository\ParticipationRepository;
use App\Entity\Participation;




class CourAdminController extends AbstractController
{
    #[Route('/admin/cours/ajouter', name: 'cour_ajouter')]
    public function ajouter(Request $request): Response
    {
        $cours = new Cours();
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            // Vérifie si un fichier a été uploadé
            if ($imageFile) {
                // Lire le contenu du fichier en tant que flux
                $imageContent = file_get_contents($imageFile->getPathname());

                // Stocker le contenu du fichier dans l'entité Produit
                $cours->setImage($imageContent);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cours);
            $entityManager->flush();

            // Ajout d'un message flash de succès
            $this->addFlash('success', 'Le cours a été ajouté avec succès.');

            // Redirection vers la page d'accueil ou une autre page de votre choix
           return $this->redirectToRoute('cour_liste');
        }

        return $this->render('GestionCours/ajouterCour.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/admin/cours/liste', name: 'cour_liste')]
    public function liste(CoursRepository $coursRepository,RatingRepository $ratingRepository,ParticipationRepository $ParticipationRepository): Response
    {
        $mostLikedCours = $ratingRepository->getMostLikedCours();
        $mostParticipant = $ParticipationRepository->getMostParticipant();
        $mostHatedCours = $ratingRepository->getMostHatedCours();
        $cours = $coursRepository->findAll(); // Récupérer tous les cours depuis la base de données

        // Vérifier la capacité de chaque cours
     // Initialiser un tableau pour stocker les noms des cours épuisés
     $coursEpuises = [];

     // Vérifier la capacité de chaque cours
     foreach ($cours as $cour) {
         if ($cour->getCapacite() <= 0) {
             // Capacité inférieure ou égale à zéro, ajouter le nom du cours au tableau
             $coursEpuises[] = $cour->getNomCour();
         }
     }
 
     // Si des cours épuisés sont trouvés, afficher une notification pour chaque cours épuisé
     foreach ($coursEpuises as $nomCour) {
         $message = sprintf(
             'La capacité du cours "%s" est maintenant épuisée.',
             $nomCour
         );
         $this->addFlash('warning', $message);
     }
    
        return $this->render('GestionCours/listeCour.html.twig', [
            'mostLikedCours' => $mostLikedCours,
            'mostParticipant' => $mostParticipant,
            'mostHatedCours' => $mostHatedCours,
            'cours' => $cours, // Passer les cours récupérés à la vue
        ]);
    }
    

    #[Route('/admin/cours/supprimer/{id}', name: 'cour_supprimer', methods: ['POST'])]
    public function supprimer(Request $request, int $id, CoursRepository $coursRepository): Response
    {
        $cour = $coursRepository->find($id);
    
        if (!$cour) {
            throw $this->createNotFoundException('Le cours avec l\'ID '.$id.' n\'existe pas.');
        }
    
        if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cour);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('cour_liste');
    }

#[Route('/admin/cours/modifier/{id}', name: 'cour_modifier')]
public function modifier(Request $request, int $id, CoursRepository $coursRepository): Response
{
    $cour = $coursRepository->find($id);

    if (!$cour) {
        throw $this->createNotFoundException('Le cours avec l\'ID '.$id.' n\'existe pas.');
    }

    $form = $this->createForm(CoursType::class, $cour);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('imageFile')->getData();

        // Vérifie si un nouveau fichier a été uploadé
        if ($imageFile) {
            // Lire le contenu du fichier en tant que flux
            $imageContent = file_get_contents($imageFile->getPathname());

            // Stocker le contenu du nouveau fichier dans l'entité Cours
            $cour->setImage($imageContent);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

         // Ajout d'un message flash de succès
         $this->addFlash('success', 'Le cours a été modifié avec succès.');

        return $this->redirectToRoute('cour_liste');
    }
    

    return $this->render('GestionCours/modifierCour.html.twig', [
        'form' => $form->createView(),
        
      
    ]);
}



#[Route('/popular-cours', name: 'popular_cours')]
    public function index(RatingRepository $ratingRepository,ParticipationRepository $ParticipationRepository ): Response
    {
        $mostLikedCours = $ratingRepository->getMostLikedCours();
        $mostParticipant = $ParticipationRepository->getMostParticipant();
        $mostHatedCours = $ratingRepository->getMostHatedCours();
        
        return $this->render('GestionCours/rate.html.twig', [
            'mostLikedCours' => $mostLikedCours,
            'mostParticipant' => $mostParticipant,
            'mostHatedCours' => $mostHatedCours,
        ]);

        


    }

    




}