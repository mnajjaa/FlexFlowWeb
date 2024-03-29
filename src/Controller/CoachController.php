<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
class CoachController extends AbstractController
{
    #[Route('/coach', name: 'coach_dashboard')]
    public function index(): Response
    {
        return $this->render('coach/index.html.twig', [
            'controller_name' => 'CoachController',
        ]);
    }

#[Route('/profileCoach', name: 'coach_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $user = new User();
        
        $email =  $request->getSession()->get(Security::LAST_USERNAME);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        // $id = $user->getId();
        // Remove the unused variable $id

        return $this->render('coach/profile.html.twig', [
            'coach' => $user,
        ]);
    }

    #[Route('/editProfileCoach', name: 'coach_edit_profile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $user = new User();
        
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        
if ($request->isMethod('POST')) {
    
    
          //code ajout image
          //les images sont stockées dans le dossier public/uploads/users 

            $file = $request->files->get('image');
            if ($file) {
                $fileName = (string)md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('uploads'), $fileName);

                // Update the user's image
                $user->setimage($fileName);
            }



            $user->setNom($request->request->get('nom'));
            // var_dump($request->request->get('nom'));
            $user->setTelephone($request->request->get('telephone'));
            //$user->setimage($fileName);
            $user->setEmail($request->request->get('email'));
            //var_dump($user);

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('coach_profile');
            
        }        
        return $this->render('coach/editProfileCoach.html.twig', [
            'coach' => $user,
        ]);
    
    }
}
 /* $email=$request->getSession()->get(Security::LAST_USERNAME);
        $user=$entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);*/