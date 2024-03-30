<?php

namespace App\Controller;

use Symfony\Component\Mailer\MailerInterface;
use App\Entity\User;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Demande;
use App\Entity\Offre;
use App\Form\DemandeFormType;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;



class FormDemandeController extends AbstractController

{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/form/demande', name: 'app_form_demande')]
       
        public function new(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
        {
            $email1 =  $request->getSession()->get(Security::LAST_USERNAME);
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email1]);
    
            $demande = new Demande();
            $demande->setUser($user);
            $demande->setNom($user->getNom());
            $form = $this->createForm(DemandeFormType::class, $demande);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                // Récupérer la maladie sélectionnée par l'utilisateur
            $maladie = $demande->getMaladieChronique();

            // Récupérer l'offre sélectionnée par l'utilisateur
            $offre = $demande->getOffre();

            // Vérifier si la maladie correspond à la spécialité de l'offre
            if ($this->isMaladieMatchingSpecialite($maladie, $offre)) {
                // Afficher un message d'erreur à l'utilisateur
                $this->addFlash('error', 'Vous ne pouvez pas vous entraîner dans cette spécialité à cause de votre maladie.');
                return $this->redirectToRoute('app_form_demande'); // Rediriger vers le formulaire pour empêcher l'enregistrement de la demande
            } 
            else {
                // Assurez-vous que l'état est correctement défini avant la persistance
                $demande->setEtat('En attente');

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($demande);
                $entityManager->flush();
                  // Envoyer un e-mail à l'utilisateur s'il est défini sur la demande
                  $user = $demande->getUser();
                  if ($user !== null) {
                      $email = (new Email())
                          ->from('votre@email.com')
                          ->to($user->getEmail())
                          ->subject('Confirmation de votre demande de coaching privé')
                          ->html('<p>Votre demande a été enregistrée avec succès.</p>');
  
                      $mailer->send($email);
                  }
  
                  return $this->redirectToRoute('demande_success');
              }
          }
  
          return $this->render('form_demande/demande.html.twig', [
              'form' => $form->createView(),
          ]);
      }
  
        private function isMaladieMatchingSpecialite(string $maladie, Offre $offre): bool
    {
        $specialite = $offre->getSpecialite();
    
        // Vérifier si la maladie correspond à la spécialité de l'offre
        switch ($specialite) {
            case 'Yoga':
                return in_array($maladie, [
                    'Maladies infectieuses contagieuses',
                    'Maladies oculaires graves',
                    'Troubles musculo-squelettiques graves',
                    'Problèmes neurologiques',
                    'Hypertension artérielle non contrôlée',
                    'Problèmes cardiaques graves',
                ]);
            case 'Boxe':
                return in_array($maladie, [
                    'Problèmes cardiaques graves',
                    'Hypertension artérielle non contrôlée',
                    'Problèmes musculo-squelettiques graves',
                    'Maladies inflammatoires',
                    'Maladies infectieuses',
                    'Problèmes respiratoires graves',
                    'Troubles de l\'alimentation',
                ]);
            case 'Musculation':
                return in_array($maladie, [
                    'Maladies cardiaques graves',
                    'Hypertension artérielle non contrôlée',
                    'Problèmes respiratoires sévères',
                    'Maladies vasculaires périphériques',
                    'Problèmes neurologiques graves',
                
                ]);
            case 'Cardio':
                return in_array($maladie, [
                    'Maladies cardiaques graves',
                    'Hypertension artérielle non contrôlée',
                    'Problèmes respiratoires sévères',
                    'Maladies vasculaires périphériques',
                    'Problèmes neurologiques graves',
                    'Diabète non contrôlé',
                    'Infections actives',
                ]);
            default:
                return false; // Si la spécialité n'est pas reconnue, considérez qu'il n'y a pas de correspondance
        }
    }
    
    
    
        #[Route('/demande/success', name: 'demande_success')]
        public function demandeSuccess(): Response
        {
            return $this->render('form_demande/demande_success.html.twig');
        }
    }