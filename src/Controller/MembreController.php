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
use App\Service\IpInfoService;


class MembreController extends AbstractController
{   
    static $mdp=false;
    private $IpInfoService;
      
    public function __construct(IpInfoService $IpInfoService)
    {
        $this->IpInfoService = $IpInfoService;


    }

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
    $erreur = false;
    $mdp = false;
    $locations = []; // Added this line to initialize the locations array

    $email =  $request->getSession()->get(Security::LAST_USERNAME);
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    $currentUserId = $user->getId(); // Get the id of the current user

    $ipinfoservice = $user->getLoginHistories(); // Get the login histories of the user
    foreach($ipinfoservice as $ipinfo){
        $ip = $ipinfo->getIpAdress();
        $loginDate = $ipinfo->getLoginDate(); // Get the login date

        // Check if the user id in the LoginHistory object matches the current user id
        if ($ipinfo->getUser()->getId() == $currentUserId) {
            $location = $this->IpInfoService->getIpInfo($ip);
            array_push($locations, array('location' => $location, 'loginDate' => $loginDate));
        }
    }

    return $this->render('membre/profile.html.twig', [
        'membre' => $user,
        'mdp' => $mdp,
        'erreur' => $erreur,
        'locations' => $locations // Added this line to pass the locations array to the template
    ]);
}
    #[Route('/editProfileMembre', name: 'membre_edit_profile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {   $erreur=false;
        $mdp=false;
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
        return $this->render('membre/profile.html.twig', [
            'membre' => $user,
            'mdp'=>$mdp,
            'erreur'=>$erreur
        ]);
    
    }
    #[Route('/editPwdMembre', name: 'membre_edit_pwd')]
    public function editPwd(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, UserPasswordHasherInterface $passwordHasher): Response
    {
        $erreur=false;
        self::$mdp=false; // Initialize mdp to false
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
                return $this->redirectToRoute('membre_profile', ['erreur'=>true]);
               
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
                return $this->redirectToRoute('membre_profile');
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
                return $this->redirectToRoute('membre_profile');
        }
        }
        return $this->render('membre/profile.html.twig', [
            'mdp'=>self::$mdp,
            'membre' => $user,
            'erreur'=>$erreur
        ]);
    
    }
    }

}
