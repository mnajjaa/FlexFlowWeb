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
use App\Form\DemandeFormType;

class FormDemandeController extends AbstractController
{
    #[Route('/form/demande', name: 'app_form_demande')]
    public function new(Request $request, MailerInterface $mailer): Response
    {
        $demande = new Demande();
        $form = $this->createForm(DemandeFormType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

        return $this->render('form_demande/demande.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/demande/success', name: 'demande_success')]
    public function demandeSuccess(): Response
    {
        return $this->render('form_demande/demande_success.html.twig');
    }
}
