<?php
// src/Service/MessageGenerator.php
namespace App\services;

use Twilio\Rest\Client;

class SmsGenerator
{
    
    public function SendSms(string $number, string $name, string $text)
    {
        
        $accountSid = $_ENV['twilio_account_sid'];  //Identifiant du compte twilio
        $authToken = $_ENV['twilio_auth_token']; //Token d'authentification
        $fromNumber = $_ENV['twilio_from_number']; // Numéro de test d'envoie sms offert par twilio

        $toNumber = $number;
         // Le numéro de la personne qui reçoit le message
        $message = 'une nouvelle offre a été ajouté'; //Contruction du sms

        //Client Twilio pour la création et l'envoie du sms
        $client = new Client($accountSid, $authToken);

        $client->messages->create(
            $toNumber,
            [
                'from' => $fromNumber,
                'body' => $message,
            ]
        );


    }
}

