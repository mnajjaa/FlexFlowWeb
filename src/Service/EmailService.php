<?php
// src/Service/EmailService.php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


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

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;



class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($to, $subject, $content)
    {
        $email = (new Email())
            ->from('bahaeddinedridi1@gmail.com')
            ->to($to)
            ->subject($subject)
            ->text($content);

        $this->mailer->send($email);
    }
}
