<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commande;
use App\Service\PDFGeneratorService;
use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
class ProduitController extends AbstractController
{
    #[Route('/', name: 'produits')]
    public function index(): Response
{
    $produits = $this->getDoctrine()->getRepository(Produit::class)->findAll();

    foreach ($produits as $produit) {
        // Convertir l'image BLOB en données binaires base64
        $produit->image = base64_encode(stream_get_contents($produit->getImage()));
    }

    return $this->render('produit/index.html.twig', [
        'produits' => $produits,
    ]);
}




#[Route('/panier', name: 'consulter_panier')]
public function consulterPanier(): Response
{
    // Récupérer les éléments du panier depuis la session
    $panier = $this->get('session')->get('panier', []);

    // Calculer le total du prix de tous les achats dans le panier
    $total = $this->calculerTotalPanier($panier);

    // Vous pouvez passer le panier et le total à la vue pour l'afficher
    return $this->render('panier/index.html.twig', [
        'panier' => $panier,
        'total' => $total, // Passer le total à la vue
    ]);
}


#[Route('/valider-panier', name: 'valider_panier')]
public function validerPanier(PDFGeneratorService $pdfGeneratorService, SessionInterface $session, RouterInterface $router): Response
{
    // Récupérer les éléments du panier depuis la session
    $panier = $session->get('panier', []);

    // Récupérer le gestionnaire d'entités
    $entityManager = $this->getDoctrine()->getManager();

    // Liste des produits achetés pour le PDF
    $productsForPDF = [];

    // Parcourir les éléments du panier
    foreach ($panier as $item) {
        // Récupérer l'ID et la quantité achetée du produit
        $produitId = $item['produit']->getId();
        $quantiteAchete = $item['quantite'];

        // Trouver le produit dans la base de données par son ID
        $produit = $entityManager->getRepository(Produit::class)->find($produitId);

        // Si le produit existe
        if ($produit) {
            // Vérifier si la quantité en stock est suffisante
            if ($produit->getQuantite() >= $quantiteAchete) {
                // Mettre à jour la quantité et la quantité vendue du produit
                $nouvelleQuantite = $produit->getQuantite() - $quantiteAchete;
                $nouvelleQuantiteVendue = $produit->getQuantiteVendues() + $quantiteAchete;
                $produit->setQuantite($nouvelleQuantite);
                $produit->setQuantiteVendues($nouvelleQuantiteVendue);
                $entityManager->persist($produit);
            } else {
                // Gérer le cas où la quantité en stock est insuffisante
                // Par exemple, afficher un message d'erreur à l'utilisateur
            }

            // Créer une nouvelle entité Commande
            $commande = new Commande();
            $commande->setDateCommande(new \DateTime()); // Date et heure actuelles
            $commande->setIdProduit($produit->getId());
            $commande->setNom($produit->getNom());
            $commande->setMontant($produit->getPrix() * $quantiteAchete);

            // Enregistrer la commande dans la base de données
            $entityManager->persist($commande);

            // Ajouter les détails du produit à la liste des produits pour le PDF
            $productsForPDF[] = [
                'nom' => $produit->getNom(),
                'quantite' => $quantiteAchete,
                'montant' => $produit->getPrix() * $quantiteAchete,
            ];
        }
    }


     // Supprimez tous les éléments du panier de la session

     // Redirigez ou renvoyez une réponse appropriée
    // Enregistrer les modifications dans la base de données
    $entityManager->flush();

    // Générer le nom du fichier PDF avec la date actuelle et l'heure
    $date = new \DateTime();
    $fileName = 'facture_' . $date->format('Y-m-d_H-i-s') . '.pdf';

    // Générer le contenu du PDF avec les détails des produits achetés
    $pdfContent = $pdfGeneratorService->generatePDF($productsForPDF);

    // Supprimer les produits achetés du panier dans la session
    $session->set('panier', []);

    // Retourner le PDF en tant que réponse HTTP
    return new Response($pdfContent, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
    ]);
    
    // Redirection vers la page des produits après le téléchargement du PDF
    
}



private function calculerTotalPanier(array $panier): float
{
    $total = 0;

    foreach ($panier as $item) {
        // Assurez-vous que l'élément du panier contient bien un produit
        if (isset($item['produit']) && $item['produit'] instanceof Produit) {
            $total += $item['produit']->getPrix() * $item['quantite'];
        }
    }

    return $total;
}

#[Route('/ajouter-au-panier/{id}', name: 'ajouter_au_panier')]
public function ajouterAuPanier(Request $request, int $id): Response
{
    // Récupérer le produit depuis la base de données
    $produit = $this->getDoctrine()->getRepository(Produit::class)->find($id);
    
    if (!$produit) {
        throw $this->createNotFoundException('Le produit avec l\'ID ' . $id . ' n\'existe pas.');
    }

    // Récupérer la quantité choisie depuis le formulaire
    $quantite = $request->request->get('quantite');

    // Vérifier si la quantité en stock est suffisante
if ($quantite > $produit->getQuantite()) {
    // La quantité en stock est insuffisante, afficher une alerte
    $response = new Response('<script>alert("La quantité en stock est insuffisante."); window.location.href = "' . $this->generateUrl('produits') . '";</script>');
    return $response;
}


    // Ajouter le produit au panier avec la quantité choisie
    $panier = $this->get('session')->get('panier', []);
    $panier[] = [
        'produit' => $produit,
        'quantite' => $quantite,
    ];
    $this->get('session')->set('panier', $panier);

    // Redirection vers la page d'accueil ou une autre page
    return $this->redirectToRoute('produits');
}






    #[Route('/incrementer-quantite/{id}', name: 'incrementer_quantite')]
    public function incrementerQuantite(int $id, SessionInterface $session): JsonResponse
    {
        $panier = $session->get('panier', []);
    
        foreach ($panier as &$item) {
            if ($item['produit']->getId() === $id) {
                if ($item['quantite'] < 5) { // Vérifie si la quantité n'a pas atteint le maximum (5)
                    $item['quantite']++;
                } else {
                    // Si la quantité atteint le maximum, affichez un message
                    return new JsonResponse(['message' => 'Vous avez atteint la quantité maximale.']);
                }
                break;
            }
        }
    
        $session->set('panier', $panier);
    
        return new JsonResponse(['success' => true]);
    }

    #[Route('/decrementer-quantite/{id}', name: 'decrementer_quantite')]
public function decrementerQuantite(int $id, SessionInterface $session): JsonResponse
{
    $panier = $session->get('panier', []);

    foreach ($panier as &$item) {
        if ($item['produit']->getId() === $id) {
            if ($item['quantite'] > 1) { // Vérifie si la quantité est supérieure à 1 pour pouvoir décrémenter
                $item['quantite']--;
            }
            break;
        }
    }

    $session->set('panier', $panier);

    return new JsonResponse(['success' => true]);
}

    
    #[Route('/supprimer-achat/{id}', name: 'supprimer_achat')]
    public function supprimerAchat(int $id, SessionInterface $session): Response
    {
        // Récupérer le panier depuis la session
        $panier = $session->get('panier', []);
    
        // Recherchez l'élément du panier correspondant à l'ID donné
        foreach ($panier as $key => $item) {
            if ($item['produit']->getId() === $id) {
                // Supprimez l'élément du panier
                unset($panier[$key]);
                break;
            }
        }
    
        // Enregistrez le panier mis à jour dans la session
        $session->set('panier', $panier);
    
        // Redirigez ou renvoyez une réponse appropriée
        return $this->redirectToRoute('consulter_panier'); // Redirige vers la page du panier, par exemple
    }
    



    #[Route('/vider-panier', name: 'vider_panier')]
public function viderPanier(SessionInterface $session): Response
{
    // Supprimez tous les éléments du panier de la session
    $session->set('panier', []);

    // Redirigez ou renvoyez une réponse appropriée
    return $this->redirectToRoute('consulter_panier'); // Redirige vers la page du panier, par exemple
}

    }
    


   





   




