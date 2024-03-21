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
                // Générez un nom de fichier unique
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
    
                // Déplacez le fichier dans le répertoire où vous souhaitez stocker les images
                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'), // Répertoire de destination (doit être configuré dans config/services.yaml)
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer les exceptions si le téléchargement échoue
                }
    
                // Mettez à jour la propriété 'image' de l'entité Cours avec le nom de fichier
                $cours->setImage($newFilename);
            }
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cours);
            $entityManager->flush();
    
            // Ajoutez ici un message flash ou redirigez l'utilisateur vers une autre page
    
            //return $this->redirectToRoute('ajouter.html.twig');
        }
    
        return $this->render('ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/admin/cours/liste', name: 'cour_liste')]
    public function liste(CoursRepository $coursRepository): Response
    {
        $cours = $coursRepository->findAll(); // Récupérer tous les cours depuis la base de données
    
        // Convertir les BLOBs en données binaires
        foreach ($cours as $cour) {
            $cour->setImage(base64_encode(stream_get_contents($cour->getImage())));
        }
    
        return $this->render('liste.html.twig', [
            'cours' => $cours, // Passer les cours récupérés à la vue
        ]);
    }
}
