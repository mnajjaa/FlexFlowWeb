<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class MembreController extends AbstractController
{
    #[Route('/membre', name: 'membre_dashboard')]
    public function index(): Response
    {
        return $this->render('membre/index.html.twig', [
            'controller_name' => 'MembreController',
        ]);
    }

    #[Route('/profileMembre', name: 'membre_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $user = new User();
        
        $email =  $request->getSession()->get(Security::LAST_USERNAME);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        // $id = $user->getId();
        // Remove the unused variable $id
        
        return $this->render('membre/profile.html.twig', [
            'membre' => $user,
        ]);
    }

    #[Route('/editProfileMembre', name: 'membre_edit_profile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $user = new User();
        
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        
          if ($request->isMethod('POST')) 
          {
    
    
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
            return $this->redirectToRoute('membre_profile');
            
        }        
        return $this->render('membre/editProfileMembre.html.twig', [
            'membre' => $user,
        ]);
    
    }
    #[Route('/editPwdMembre', name: 'membre_edit_pwd')]
    public function editPwd(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, UserPasswordHasherInterface $passwordHasher): Response
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        
        if($request->isMethod('POST')) {
            if($request->get('plainPassword') != $request->get('plainPasswordConfirm')){
                $this->addFlash('reset_password_error', 'Passwords do not match');
                return $this->redirectToRoute('membre_profile');
            }
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $request->get('plainPassword')
            );
    
            $user->setPassword($encodedPassword);
                $entityManager->flush();
                var_dump($user);
                return $this->redirectToRoute('membre_profile');
        }
        return $this->render('membre/editPwdMembre.html.twig');
    
    }

}
