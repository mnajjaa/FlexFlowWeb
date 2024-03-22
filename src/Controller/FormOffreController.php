<?php

namespace App\Controller;
use App\Entity\Offre;
use App\Form\OffreType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Form\FormOffreCType;
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
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($offre);
            $entityManager->flush();

            // Envoyer un e-mail à l'utilisateur
            $email = (new Email())
                ->from('votre@email.com')
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

    /**
     * @Route("/offre/success", name="offre_success")
     */
    public function success(): Response
    {
        return $this->render('form_offre/success.html.twig');
    }
}