<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commande;
use App\Service\PDFGeneratorService;
use Symfony\Component\Routing\RouterInterface;
use App\Service\SmsGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;


use App\Repository\ProduitRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProduitController extends AbstractController
{
    #[Route('/vitrine', name: 'produits')]
    public function index(Request $request, PaginatorInterface $paginator, ProduitRepository $produitRepository): Response
    {
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findAll();
       

   

        foreach ($produits as $produit) {
            // Convertir l'image BLOB en données binaires base64
            $produit->image = base64_encode(stream_get_contents($produit->getImage()));
        }
      // Pagination
      $pagination = $paginator->paginate(
        $produits,
        $request->query->getInt('page', 1),
        6 // Limite par page
    );
        // Comptez le nombre de produits de type "Accessoires"
        $accessoiresCount = 0;
        $jeuxCount = 0;
        $vitaminesCount = 0;
        $proteineCount = 0;
        $vetementsCount = 0;
        $FruitsCount = 0;
        foreach ($produits as $produit) {
            if ($produit->getType() === 'Accessoires') {
                $accessoiresCount++;
            }
            if ($produit->getType() === 'jeux') {
                $jeuxCount++;
            }
            if ($produit->getType() === 'Vitamines') {
                $vitaminesCount++;
            }
            if ($produit->getType() === 'Proteine') {
                $proteineCount++;
            }
            if ($produit->getType() === 'vetements') {
                $vetementsCount++;
            }
            if ($produit->getType() === 'Fruits') {
                $FruitsCount++;
            }
        }

        usort($produits, function ($a, $b) {
            return $b->getQuantiteVendues() - $a->getQuantiteVendues();
        });
    
        // Récupérer les trois premiers produits (les plus vendus)
        $topProduits = array_slice($produits, 0, 3);

        return $this->render('GestionProduit/produit/index.html.twig', [
            'pagination' => $pagination,

            'produits' => $produits,
            'accessoiresCount' => $accessoiresCount,
            'jeuxCount' => $jeuxCount,
            'vitaminesCount' => $vitaminesCount,
            'proteineCount' => $proteineCount,
            'vetementsCount' => $vetementsCount,
        'topProduits' => $topProduits,
        'FruitsCount' => $FruitsCount,

        ]);
    }



    




    







    #[Route('/produit/{id}', name: 'produit_detail')]
    public function showProductDetail($id): Response
    {
        $produit = $this->getDoctrine()->getRepository(Produit::class)->find($id);
    
        // Vérifiez si le produit existe
        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }
    
        // Convertir l'image BLOB en données binaires base64
        $produit->image = base64_encode(stream_get_contents($produit->getImage()));
    
        return $this->render('GestionProduit/produit/detailProduit.html.twig', [
            'produit' => $produit,
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
        return $this->render('GestionProduit/panierFront.html.twig', [
            'panier' => $panier,
            'total' => $total, // Passer le total à la vue
        ]);
    }


    



    private function calculerTotalPanier(array $panier): float
    {
        $total = 0;

        foreach ($panier as $item) {
            // Assurez-vous que l'élément du panier contient bien un produit
            if (isset ($item['produit']) && $item['produit'] instanceof Produit) {
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

         // Renvoyer le nombre total de produits dans le panier
    $totalProduits = count($panier);
    return $this->json(['totalProduits' => $totalProduits]);
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

        // Retournez une réponse JSON pour indiquer que le panier a été vidé
        return new JsonResponse(['message' => 'Le panier a été vidé avec succès']);
    }












    #[Route('/payment', name: 'payment')]
    public function index1(): Response
    {
        return $this->render('GestionProduit/payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }


    #[Route('/checkout', name: 'checkout')]
    public function checkout($stripeSK): Response
    {
        Stripe::setApiKey($stripeSK);

        // Récupérer les éléments du panier depuis la session
        $panier = $this->get('session')->get('panier', []);

        // Préparer les éléments de la session Stripe à partir des produits dans le panier
        $lineItems = [];
        foreach ($panier as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item['produit']->getNom(),
                    ],
                    'unit_amount' => round($item['produit']->getPrix() * 100 / 3),
                ],
                'quantity' => $item['quantite'],
            ];
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }



    #[Route('/success-url', name: 'success_url')]
    public function validerPanier(PDFGeneratorService $pdfGeneratorService, Request $request, SmsGenerator $smsGenerator, SessionInterface $session, RouterInterface $router, MailerInterface $mailer, \Twig\Environment $twig): Response
    {
        // Récupérer les éléments du panier depuis la session
        $panier = $session->get('panier', []);
    
        // Récupérer le gestionnaire d'entités
        $entityManager = $this->getDoctrine()->getManager();
    
        // Liste des produits achetés pour le PDF
        $productsForPDF = [];
        $dateActuelle = date('d/m/Y');
        $montantTotal = 0; // Initialisation du montant total
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

                     // Calculer le montant pour ce produit
                $montantProduit = $produit->getPrix() * $quantiteAchete;
                $montantTotal += $montantProduit; // Ajouter au montant total
                } else {
                    // Gérer le cas où la quantité en stock est insuffisante
                    // Par exemple, afficher un message d'erreur à l'utilisateur
                }
               // kifeh tejbed luser m session
               $user = new User();
        $email=$request->getSession()->get(Security::LAST_USERNAME);
        $user=$entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);
    
                // Créer une nouvelle entité Commande
                $commande = new Commande();
                $commande->setDateCommande(new \DateTime()); // Date et heure actuelles
                $commande->setIdProduit($produit->getId());
                $commande->setNom($produit->getNom());
                $commande->setMontant($produit->getPrix() * $quantiteAchete);
                $commande->setNomUser($user->getNom());

                // Enregistrer la commande dans la base de données
                $entityManager->persist($commande);
    
                // Ajouter les détails du produit à la liste des produits pour le PDF
                $productsForPDF[] = [
                    'nom' => $produit->getNom(),
                    'quantite' => $quantiteAchete,
                    'prix'=> $produit->getPrix(),
                    'montant' => $produit->getPrix() * $quantiteAchete,
                    'montantTotale' => $montantProduit,
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
    
        // Vérifier que $productsForPDF contient les données correctes
//var_dump($productsForPDF);
//Api sms 
$number="29678226";
$name="FlexFlow";

$text = "Bonjour,

Votre commande sera prête à être retirée. Vous pouvez venir la récupérer à tout moment.

Votre commande est valable pendant une semaine à partir d'aujourd'hui " . date('d/m/Y');


$number_test=$_ENV['twilio_to_number'];// Numéro vérifier par twilio. Un seul numéro autorisé pour la version de test.

//Appel du service
$smsGenerator->sendSms($number_test ,$name,$text);
// Générer le contenu du PDF avec les détails des produits achetés
$pdfContent = $pdfGeneratorService->generatePDF($productsForPDF);

// Vérifier le contenu généré du PDF
//($pdfContent);
 // kifeh tejbed luser m session
        $user = new User();
        $email1=$request->getSession()->get(Security::LAST_USERNAME);
        $user=$entityManager->getRepository(User::class)->findOneBy(['email'=>$email1]);
        $nomUtilisateur = $user->getNom();

        $emailContent = "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Confirmation d'achat</title>
        <style>
          body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
          }
          .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
          }
          h1 {
            color: #333;
            text-align: center;
          }
          p {
            margin-bottom: 15px;
          }
          .signature {
            font-style: italic;
          }
        </style>
        </head>
        <body>
        <div class='container'>
          <h1>Confirmation d'achat</h1>
          <p>Bonjour $nomUtilisateur,</p>
          <p>Votre commande sera prête à être retirée. Vous pouvez venir la récupérer à tout moment.</p>
          <p>Votre commande est valable pendant une semaine à partir d'aujourd'hui $dateActuelle.</p>
          <p class='signature'>Cordialement,<br>FlexFlow</p>
        </div>
        </body>
        </html>";
        
        

// Envoyer un e-mail à l'utilisateur avec le PDF en pièce jointe
$email = (new Email())
     ->from('FlexFlow <your_email@example.com>')
    ->to($email1)
    ->subject("Confirmation d'achat")
    ->html($emailContent)
    // Ajouter le PDF en pièce jointe
    ->attach($pdfContent, $fileName, 'application/pdf');

// Envoyer l'e-mail avec le PDF en pièce jointe
$mailer->send($email);

    
        // Redirection vers la page des produits après le téléchargement du PDF
       // return new Response("oooo");
        // Retourner la réponse rendue par Twig
        $session->set('panier', []);

        return new Response($twig->render('GestionProduit/success.html.twig'));
    }
    


    #[Route('/cancel-url', name: 'cancel_url')]
    public function cancelUrl(): Response
    {
        return $this->render('GestionProduit/payment/cancel.html.twig', []);
    }
/*
#[Route('/charge', name: 'charge')]
public function indexAction(Request $request)
{
    \Stripe\Stripe::setApiKey("sk_test_51OopTSDtHS4Nu6kaTroMy6hwN1MKCBKitrzK3lm26xblje6CYwCiHccuY5VB1uNQppoCOSn6C6u92jn7i6zjLikl00zSebwoIU");
   
    \Stripe\Charge::create(array(
        "amount" => 2000,
        "currency" => "usd",
        "source" => $request->request->get('stripeToken'),// the token stored in your frontend if you collect it. Should be the 
        "description" => "Paiment de test",
    ));
    return $this->render('GestionProduit/stripe.html.twig');
}




*/





#[Route('/show-button', name: 'show_button')]
public function showButton(): Response
{
    return $this->render('button.html.twig');
}

}