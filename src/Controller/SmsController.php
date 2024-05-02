<?php

namespace App\Controller;

use App\services\SmsGenerator; // Import de la classe SmsGenerator
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SmsController extends AbstractController
{
    #[Route('/sms', name: 'app_sms')]
    
    public function sms(SmsGenerator $SmsGenerator, \Twig\Environment $twig): Response
    {
        // Numéro de téléphone du destinataire
        $number = "29678226";
        // Nom de l'expéditeur
        $name = "FlexFlow";
        // Message SMS à envoyer
        $text = "Bonjour,

        Votre commande sera prête à être retirée. Vous pouvez venir la récupérer à tout moment.

        Votre commande est valable pendant une semaine à partir d'aujourd'hui " . date('d/m/Y');

        // Numéro de test pour Twilio
        $number_test = $_ENV['twilio_to_number'];

        // Envoi du SMS via le service SmsGenerator
        $SmsGenerator->sendSms($number_test, $name, $text);

        // Retourner "ok" en réponse
        return new Response("ok");
    }
}
