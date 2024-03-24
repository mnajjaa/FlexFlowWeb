<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\Response\ResponseStream;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/listeMembres', name: 'liste_membres')]
    public function membres(UserRepository  $userRepository): Response
    {  
        $membres=$userRepository->findByRole("MEMBRE");
        //var_dump($membres);


        return $this->render('admin/listeMembres.html.twig', [
            'controller_name' => 'AdminController',
            'membres'=>$membres
        ]);
    }

    #[Route('/listeCoaches', name: 'liste_coaches')]
    public function coaches(UserRepository  $userRepository): Response
    {  
        $coaches=$userRepository->findByRole("COACH");
        //var_dump($membres);
        return $this->render('admin/listeCoaches.html.twig', [
            'controller_name' => 'AdminController',
            'coaches'=>$coaches
        ]);
    }
    #[Route('/addCoach', name: 'add_coach')]
    public function addCoachForm(): Response
    {
        
        return $this->render('admin/addCoach.html.twig');
    }

    #[Route('/coachadd', name: 'coach_add')]
    public function addCoach(EntityManagerInterface $entityManager,Request $request,MailerInterface $mailerInterface,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $coach = new User();
       $email= $request->request->get('email');
       $nom= $request->request->get('name');
       $telephone= $request->request->get('telephone');

           
            $coach->setRoles(["COACH"]);
            $coach->setEmail($email);
            $coach->setNom($nom);
            $coach->setTelephone($telephone);
            $password = base64_encode(random_bytes(8));
            $coach->setPassword( $userPasswordHasher->hashPassword(
                $coach,
                $password
            ));
            //var_dump($password);
            // ...
                // Send email to the user
                $email = (new Email())
                    ->from('bahaeddinedridi1@gmail.com')
                    ->to($email)
                    ->subject('Your Generated Password')
                    ->text('Your password: ' . $password);

                $mailerInterface->send($email);
        

            $entityManager->persist($coach);
            $entityManager->flush();
            
        
        return $this->redirectToRoute('liste_coaches');
        
        
    }
}


}