<?php

namespace App\Controller;
use App\Entity\Offre;
use App\Form\FormOffreCType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class FormOffreController extends AbstractController
{
    #[Route('/form/offre', name: 'app_form_offre')]
    public function new(Request $request, MailerInterface $mailer): Response
    {
        $offre = new Offre();
        $form = $this->createForm(FormOffreCType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer l'offre dans la base de données
          //  Elle récupère le service Doctrine depuis le conteneur de services.
          //retourne l'instance de l'EntityManager associée à votre application Symfony.
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($offre);
            $entityManager->flush();

            // Envoyer un e-mail à l'utilisateur
            $email = (new Email())
                ->from('FlexFlow <your_email@example.com>')
                ->to($offre->getEmail())
                ->subject('Confirmation de votre offre')
                ->html('<p>Votre offre a été enregistrée avec succès.</p>');

            $mailer->send($email);

            return $this->redirectToRoute('offre_success');
        }

        return $this->render('form_offre/offre.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
        


    }

     
      #[Route('/offre/success', name: 'offre_success')]
    public function success(): Response
    {
        return $this->render('form_offre/success.html.twig');
    }
        

    #[Route('/form/offres', name: 'app_consulter_offres')]
    public function consulterOffres(Request $request): Response
    {
        $offres = $this->getDoctrine()->getRepository(Offre::class)->findAll();

        return $this->render('form_offre/consulter_offres.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/modifier/offre/{id}', name: 'modifier_offre')]
    public function modifierOffre(Request $request, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $offre = $entityManager->getRepository(Offre::class)->find($id);
    
        if (!$offre) {
            throw $this->createNotFoundException(
                'Aucune offre trouvée pour l\'id ' . $id
            );
        }
    
        $form = $this->createForm(FormOffreCType::class, $offre);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Les valeurs du formulaire sont automatiquement mises à jour dans l'objet $offre
            $entityManager->flush();
    
            $this->addFlash('success', 'Offre modifiée avec succès.');
    
            return $this->redirectToRoute('app_consulter_offres');
        }
    
        return $this->render('form_offre/modifier_offre.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/supprimer/offre/{id}', name: 'supprimer_offre')]
    public function supprimerOffre(Request $request, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $offre = $entityManager->getRepository(Offre::class)->find($id);

        if (!$offre) {
            throw $this->createNotFoundException(
                'Aucune offre trouvée pour l\'id ' . $id
            );
        }

        $entityManager->remove($offre);
        $entityManager->flush();

        $this->addFlash('success', 'Offre supprimée avec succès.');

        return $this->redirectToRoute('app_consulter_offres');
    }
}



