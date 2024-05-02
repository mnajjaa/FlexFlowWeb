<?php
namespace App\Service;

use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;

class ScheduledTask
{
    private $logger;
    private $userRepository;
    private $mailer;
    private $twig;

    public function __construct(LoggerInterface $logger, UserRepository $userRepository, MailerInterface $mailer, Environment $twig)
    {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function runTask()
    {
        $entityManager = $this->userRepository->getEntityManager();
        $users1 = $this->userRepository->findByMdpHasExPired();
        foreach($users1 as $user){
            $user->setIsVerified(false);
            $entityManager->flush();
        }
        $users = $this->userRepository->findByMdpExPire();
        foreach ($users as $user) {
            $this->logger->info('Sending email to ' . $user->getEmail());

            // Render the Twig template
            $body = $this->twig->render('reset_password/expirationWarning.html.twig', ['user' => $user]);

            $email = (new \Symfony\Component\Mime\Email())
                ->from('FlexFlow <bahaeddinedridi1@gmail.com>')
                ->to($user->getEmail())
                ->subject('Time to change your password')
                ->html($body);

            $this->mailer->send($email);
        }
    }
}