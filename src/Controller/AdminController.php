<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Produit;
use App\Form\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\CoursRepository;
use Symfony\Component\HttpFoundation\BinaryFileResponse;



class AdminController extends AbstractController
{
    #[Route('/admin/ajouter', name: 'produit_ajouter')]
    public function ajouter(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();
    
            // Vérifie si un fichier a été uploadé
            if ($imageFile) {
                // Lire le contenu du fichier en tant que flux
                $imageContent = file_get_contents($imageFile->getPathname());
    
                // Stocker le contenu du fichier dans l'entité Produit
                $produit->setImage($imageContent);
            }
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();
    
            // Ajoutez ici un message flash ou redirigez l'utilisateur vers une autre page
    
            //return $this->redirectToRoute('ajouter.html.twig');
        }
    
        return $this->render('ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
