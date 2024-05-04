<?php

namespace App\Controller;

use App\Entity\RoleEnum;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
   

    public function __construct(EmailVerifier $emailVerifier)
    {
        
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,SessionInterface $session): Response
    {
        //var_dump(self::$user1);
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
       // kifeh tejbed luser m session
       /* $email=$request->getSession()->get(Security::LAST_USERNAME);
        $user=$entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);*/

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
           
            $user->setRoles(["MEMBRE"]);
            $user->setImage("7c62c977256064c61946037d427a0e0c.png");
            $user->setMfaEnabled(false);
            $user->setMfaSecret(null);
            $user->setMdpExp(new \DateTime('+30 days'));
            $user->setCreatedAt(new \DateTime());
            $entityManager->persist($user);
            $entityManager->flush();
            $session->set('user',$user);

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('mnajjaibtihel@gmail.com', 'FlexFlow'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an emai
            //return $this->redirectToRoute('_profiler_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    #[Route('/registerCoach', name: 'app_register_coach')]
    public function registerCoach(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,SessionInterface $session): Response
    {
        //var_dump(self::$user1);
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
           
            $user->setRoles(["COACH"]);
            $user->setCreatedAt(new \DateTime());
            $entityManager->persist($user);
            $entityManager->flush();
            $session->set('user',$user);

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('mnajjaibtihel@gmail.com', 'FlexFlow'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an emai
            //return $this->redirectToRoute('_profiler_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
public function verifyUserEmail(Request $request, EmailVerifier $emailVerifier, TranslatorInterface $translator, SessionInterface $session,EntityManagerInterface $entityManager): Response
{
    // Validate the email confirmation link
    try {
        $emailVerifier->handleEmailConfirmation($request, $session->get('user'));
    } catch (VerifyEmailExceptionInterface $exception) {
        $this->addFlash('verify_email_error', $exception->getReason());

        return $this->redirectToRoute('app_register');
    }

    // Mark the user as verified
    $user = $session->get('user');
    $user->setIsVerified(true);
    $entityManager->flush();

    // Add a success message
    $this->addFlash('success', $translator->trans('Your email address has been verified.'));

    // Always redirect the user to the login page
    return $this->redirectToRoute('app_login');
}

#[Route(path: '/emailVerification', name: 'emailVerification')]
public function emailVerification(Request $request, EntityManagerInterface $entityManager): Response
{
    // $email=$request->getSession()->get(Security::LAST_USERNAME);
    // $user=$entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);
    // $user->setIsVerified(true);
    // $entityManager->persist($user);
    // $entityManager->flush();
    // return $this->redirectToRoute('app_login');

       

        return $this->render('registration/emailVerification.html.twig', [
           
        ]);
    
} 

     
}
