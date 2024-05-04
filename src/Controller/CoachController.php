<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class CoachController extends AbstractController
{   
    static $mdp=false;

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
    #[Route('/editPwdCoach', name: 'coach_edit_pwd')]
    public function editPwd(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, UserPasswordHasherInterface $passwordHasher): Response
    {
       
    $erreur=false;
    
    $email = $request->getSession()->get(Security::LAST_USERNAME);
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    
    
    if($request->isMethod('POST')) {
        
        if ( $request->get('actualPassword') && !$passwordHasher->isPasswordValid($user, $request->get('actualPassword'))) {
            // $this->addFlash('reset_password_error', 'Old password is incorrect');
            ?>

            <script>
                alert("Old password is incorrect");
                </script>
            <?php
            return $this->redirectToRoute('coach_edit_profile', ['erreur'=>true]);
           
        }
        else {
        ?>
        
        <?php
            self::$mdp=true;
            // return $this->render('admin/editProfileAdmin.html.twig', [
            //     'mdp'=>self::$mdp,
            //     'admin' => $user
            // ]);
        }
        if($request->get('plainPassword')  && $request->get('plainPasswordConfirm')){
          
        
        if($request->get('plainPassword') != $request->get('plainPasswordConfirm')){
            ?>
            <script>
                alert("Passwords match");
                </script>
            <?php
            return $this->redirectToRoute('coach_edit_profile');
        }
        else{
            ?>
            <script>
                alert("Passwords match");
                </script>
            <?php
        var_dump($request->get('plainPassword'));
        $encodedPassword = $passwordHasher->hashPassword(
            $user,
            $request->get('plainPassword')
        );

        $user->setPassword($encodedPassword);
        $entityManager->persist($user);
            $entityManager->flush();
            var_dump($user);
            return $this->redirectToRoute('coach_edit_profile');
    }
    }
    return $this->render('coach/editProfileCoach.html.twig', [
        'mdp'=>self::$mdp,
        'coach' => $user,
        'erreur'=>$erreur
    ]);

}


    }
}
 /* $email=$request->getSession()->get(Security::LAST_USERNAME);
        $user=$entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);*/