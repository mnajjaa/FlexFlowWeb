<?php

namespace App\Security;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use App\Entity\LoginHistory;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;   
use Symfony\Component\Security\Core\Exception\AuthenticationException;
 
class AppCustomAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;
    private  $entityManager;
    public function __construct(private UrlGeneratorInterface $urlGenerator,EntityManagerInterface $entityManager)
    {
        $this->entityManager=$entityManager;

    }
    

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
        
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_username', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);
    
        
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('_password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
        
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
         $loginHistory = new LoginHistory();
        $loginHistory->setLoginDate(new \DateTime());
        $userAgent = $request->headers->get('User-Agent');
        $loginHistory->setNavigateur($userAgent);
        $os = php_uname('s');
        $loginHistory->setSysExp($os);
        
          $publicIp = file_get_contents('http://httpbin.org/ip');
            $publicIp = json_decode($publicIp);
            $publicIp = $publicIp->origin;
         $loginHistory->setIpAdress($publicIp);

        $loginHistory->setUser($token->getUser());
        $this->entityManager->persist($loginHistory);
        $this->entityManager->flush();

        
        if($targetPath = $this->getTargetPath($request->getSession(), $firewallName)){
            return new RedirectResponse($targetPath);
        }
        $user = $token->getUser();
        $email1=$request->getSession()->get(Security::LAST_USERNAME);
        $user1=$this->entityManager->getRepository(User::class)->findOneBy(['email'=>$email1]);
        if($user1->isMfaEnabled()){
            return new RedirectResponse($this->urlGenerator->generate('app_login_otp'));
        }
        else {

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

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }
    
        $url = $this->urlGenerator->generate('app_login');
    
        return new RedirectResponse($url);
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}
