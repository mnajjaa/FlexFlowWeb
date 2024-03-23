<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Produit;
use App\Entity\Commande;
use App\Service\PDFGeneratorService;
use Symfony\Component\Routing\RouterInterface;
use App\Service\SmsGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EmailController extends AbstractController
{
    
    #[Route('/envoyer-email', name: 'envoyer-email')]

    public function envoyerEmail(MailerInterface $mailer)
    {
        // Créez l'objet Email
        $email = (new Email())
            ->from('bahaeddinedridi1@gmail.com')
            ->to('houssinebenarous48@gmail.com') // Remplacez 'email' par l'adresse e-mail du destinataire
            ->subject('Confirmation de votre offre')
            ->html('<p>Votre offre a été enregistrée avec succès.</p>');

        // Envoyez l'e-mail en utilisant le service Mailer
        $mailer->send($email);

        // Affichez un message de confirmation
        $message = 'L\'e-mail a été envoyé avec succès.';
        
        // Retournez une réponse avec le message de confirmation
        return new Response($message);
    }
}
