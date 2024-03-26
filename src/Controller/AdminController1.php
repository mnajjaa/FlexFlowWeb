<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\CoursRepository;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class AdminController1 extends AbstractController
{
    #[Route('/admin/ajouter', name: 'produit_ajouter')]
    public function ajouter(Request $request,MailerInterface $mailer): Response
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
    


             

          return $this->redirectToRoute('produit-liste');
      }

    
            // Ajoutez ici un message flash ou redirigez l'utilisateur vers une autre page
    
        



    
        return $this->render('GestionProduit/crud/ajouter.html.twig', [
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
        return $this->render('GestionProduit/crud/liste.html.twig', [
            'produits' => $produits, // Passer les produits récupérés à la vue
        ]);
    }


    #[Route('/admin/listecommande', name: 'commande-liste')]
    public function listecommande(CommandeRepository $commandeRepository, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): Response
    {

        

        // Récupérer la date d'aujourd'hui
    // Récupérer la date d'aujourd'hui
    $aujourdHui = new \DateTime();

    // Récupérer les commandes passées aujourd'hui
    $commandesAujourdHui = $commandeRepository->findBy([
        'dateCommande' => $aujourdHui
    ]);

    // Calculer le montant total des commandes d'aujourd'hui
    $montantTotalAujourdHui = 0;
    foreach ($commandesAujourdHui as $commande) {
        $montantTotalAujourdHui += $commande->getMontant();
    }

        $commandes = $commandeRepository->findAll();
        $montantTotal = 0;
    
        foreach ($commandes as $commande) {
            $montantTotal += $commande->getMontant();
        }

        $nomsUtilisateurs = $entityManager->createQueryBuilder()
        ->select('c.nomUser')
        ->from(Commande::class, 'c')
        ->getQuery()
        ->getResult();

    // Compter la fréquence de chaque nom d'utilisateur
    $nomsUtilisateursCounts = array_count_values(array_column($nomsUtilisateurs, 'nomUser'));

    // Trouver le nom d'utilisateur le plus fréquemment répété
    $nomUtilisateurPlusRepete = array_search(max($nomsUtilisateursCounts), $nomsUtilisateursCounts);
    
        // Récupérer le produit le plus vendu
        $produitPlusVendu = $produitRepository->findMostSoldProduct();
        $produitMoinsVendu = $produitRepository->findLeastSoldProduct();

        return $this->render('GestionProduit/crud/commandeTable.html.twig', [
            'commandes' => $commandes,
            'montantTotal' => $montantTotal,
            'montantTotalAujourdHui' => $montantTotalAujourdHui,
            'nomUtilisateurPlusRepete' => $nomUtilisateurPlusRepete,

            'produitPlusVendu' => $produitPlusVendu,
            'produitMoinsVendu' => $produitMoinsVendu,  
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
    
        return $this->render('GestionProduit/crud/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    



}
