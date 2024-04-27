<?php
namespace App\Service;
use App\Repository\UserRepository;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ScheduledTask
{
    private $logger;
    private $userRepository;
    private $mailer;
    public function __construct(LoggerInterface $logger,UserRepository $userRepository,MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->userRepository=$userRepository;
        $this->mailer=$mailer;
    }
    
        
    public function runTask()
    {
        $users=$this->userRepository->findByMdpExPire();
        foreach($users as $user)
        {
            $this->logger->info('Sending email to '.$user->getEmail());
            $email = (new Email())
            ->from('bahaeddinedridi1@gmail.com')
            ->to($user->getEmail())
            ->subject('Time to change your password')
            ->text('Your password will expire in 7 days. Please change it.');
            $this->mailer->send($email);
        
    }
}
}
?>


