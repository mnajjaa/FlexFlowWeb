<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\services\SmsGenerator; // Import de la classe SmsGenerator
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twilio\Rest\Client;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;

class SmsCoachingController extends AbstractController
{      
    #[Route('/sms/coaching', name: 'app_sms_coaching')]
    public function index(): Response
    {
        return $this->render('sms_coaching/index.html.twig', [
            'controller_name' => 'SmsCoachingController',
        ]);
    }
    
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

    private $twilioAccountSid = 'AC8802c2e9768e4876ace30c6beb9ba980'; // Remplacez par votre SID Twilio
   private $twilioAuthToken = '6ecb828326a724faf557c7f259f06ef5'; // Remplacez par votre jeton d'authentification Twilio
   private $twilioPhoneNumber = '+19497102963'; // Remplacez par votre numéro de téléphone Twilio

    public function sendSMS(int $id, EntityManagerInterface $entityManager)
    {
        // Initialiser le client Twilio
        $twilioClient = new Client($this->twilioAccountSid, $this->twilioAuthToken);

        // Trouver la réservation basée sur l'ID
        $reservation = $entityManager->getRepository(Reservation::class)->find($id);

        // Vérifier si la réservation existe
        if (!$reservation) {
            throw $this->createNotFoundException('La réservation avec l\'ID ' . $id . ' n\'existe pas.');
        }

        // Récupérer les informations du participant, de l'événement et de la date de réservation
        $nomParticipant = $reservation->getNomParticipant();
        $nomEvenement = $reservation->getNomEvenement();
        $dateReservation = $reservation->getDateReservation()->format('Y-m-d');

        // Numéro de téléphone de destination
        $toPhoneNumber = '+21653602680'; // Numéro de téléphone réel

        // Corps du message SMS
        $messageBody = sprintf(
            "Bienvenue %s à l'événement %s. Votre réservation est confirmée  le %s.",
            $nomParticipant,
            $nomEvenement,
            $dateReservation
        );

        // Envoyer le message SMS
        $message = $twilioClient->messages->create(
            $toPhoneNumber,
            [
                'from' => $this->twilioPhoneNumber,
                'body' => $messageBody
            ]
        );

        // Afficher le SID du message envoyé
        echo 'Message SID: ' . $message->sid;
    }

}
