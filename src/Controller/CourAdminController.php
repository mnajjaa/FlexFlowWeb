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
            return $this->redirectToRoute('cour_ajouter');
        }

        return $this->render('ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/admin/cours/liste', name: 'cour_liste')]
    public function liste(CoursRepository $coursRepository): Response
    {
        $cours = $coursRepository->findAll(); // Récupérer tous les cours depuis la base de données
    
        return $this->render('liste.html.twig', [
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
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('cour_liste');
    }

    return $this->render('modifier.html.twig', [
        'form' => $form->createView(),
    ]);
}




}