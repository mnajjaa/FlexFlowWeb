<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
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
    
            return $this->redirectToRoute('produit-liste');
        }
    
        return $this->render('crud/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/admin/liste', name: 'produit-liste')]
    public function liste(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll(); // Récupérer tous les produits depuis la base de données
    
        foreach ($produits as $produit) {
            // Vérifier si l'image existe
            if ($produit->getImage()) {
                // Convertir les données binaires en base64
                $imageData = base64_encode(stream_get_contents($produit->getImage()));
                $produit->setImage($imageData);
            }
        }
        return $this->render('crud/liste.html.twig', [
            'produits' => $produits, // Passer les produits récupérés à la vue
        ]);
    }



    #[Route('/admin/cours/supprimer/{id}', name: 'produit-supprimer', methods: ['POST'])]
    public function supprimer(Request $request, int $id, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find($id);
    
        if (!$produit) {
            throw $this->createNotFoundException('Le produit avec l\'ID '.$id.' n\'existe pas.');
        }
    
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('produit-liste');
    }




    #[Route('/admin/modifier/{id}', name: 'produit_modifier')]
    public function modifier(Request $request, int $id, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find($id);
    
        if (!$produit) {
            throw $this->createNotFoundException('Le produit avec l\'ID '.$id.' n\'existe pas.');
        }
    
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
    
            return $this->redirectToRoute('produit-liste');
        }
    
        return $this->render('crud/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    



}
