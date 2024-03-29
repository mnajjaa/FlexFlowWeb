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

#[Route('/profile', name: 'coach_profile')]
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

    #[Route('/editProfile', name: 'coach_edit_profile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $user = new User();
        
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        
if ($request->isMethod('POST')) {
           


            $user->setNom($request->request->get('nom'));
            $user->setTelephone($request->request->get('telephone'));
            $user->setimage($request->request->get('image'));
            $user->setEmail($request->request->get('email'));

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('coach_profile');
        }        
        return $this->render('coach/editProfile.html.twig', [
            'coach' => $user,
        ]);
    }
}
 /* $email=$request->getSession()->get(Security::LAST_USERNAME);
        $user=$entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);*/