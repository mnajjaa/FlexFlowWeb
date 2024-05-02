<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use App\Service\TwoFactorAuthenticator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends AbstractController
{
    private $TwoFactorAuthenticator;
    private $urlGenerator;
    public function __construct( TwoFactorAuthenticator $TwoFactorAuthenticator,UrlGeneratorInterface $urlGenerator)
    {
        $this->TwoFactorAuthenticator=$TwoFactorAuthenticator;
        $this->urlGenerator=$urlGenerator;
    }
    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    #[Route(path: '/loginOTP', name: 'app_login_otp')]
    public function loginOTP(AuthenticationUtils $authenticationUtils): Response
    {
        

        return $this->render('login/loginOTP.html.twig',[
            "error"=>""
        ]);
    }
    #[Route(path: '/loginOTP123', name: 'valide_code_otp')]
    public function valideOTP(Request $request,EntityManagerInterface $entityManager): ?Response
    {
        
        

        if($request->isMethod('POST'))
        {
        
            $code=$request->request->get('otp');
            $email = $request->getSession()->get(Security::LAST_USERNAME);
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            
            if($this->TwoFactorAuthenticator->validateOTPCode($user->getmfaSecret(),$code)){
             
                
                if($user->getRoles() === ['ADMIN']){

                    return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
                }
                if($user->getRoles() === ['COACH']){
                    return new RedirectResponse($this->urlGenerator->generate('coach_dashboard'));
                }
                if($user->getRoles() === ['MEMBRE']){
                    return new RedirectResponse($this->urlGenerator->generate('membre_dashboard'));
                }
            }
            }
            else 
            {
                
                return $this->render('login/loginOTP.html.twig',[
                    "error"=>"Code OTP invalide"
                ]       );
            }
            
        
        

        

        return $this->render('login/loginOTP.html.twig');
        
    }



    // #[Route(path: '/login/user', name: 'app_login')]
    // public function login(AuthenticationUtils $authenticationUtils): Response
    // {
    //     // get the login error if there is one
    //     $error = $authenticationUtils->getLastAuthenticationError();

    //     // last username entered by the user
    //     $lastUsername = $authenticationUtils->getLastUsername();

    //     return $this->render('login/login-user.html.twig', [
    //         'last_username' => $lastUsername,
    //         'error' => $error,
    //     ]);
    // }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/accessD', name: 'accessD')]
    public function accessD(AuthenticationUtils $authenticationUtils): Response
    {
       

        return $this->render('login/denied.html.twig', [
           
        ]);
    }

   

}
