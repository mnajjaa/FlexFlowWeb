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
use App\Form\RegistrationFormType;
use App\Form\UserType;
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


#[Route('/editCoach/{id}', name: 'edit_coach')]
public function editCoach(Request $request, int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
{
    $coach = $userRepository->find($id);
if (!$coach) {
    throw $this->createNotFoundException(
        'No coach found for id '.$id
    );

}
if($request->isMethod('POST')){
    $email= $request->request->get('email');
    $nom= $request->request->get('name');
    $telephone= $request->request->get('telephone');
   
    $coach->setEmail($email);
    $coach->setNom($nom);
    $coach->setTelephone($telephone);
    $coach->setRoles(["COACH"]);
    $coach->setPassword($coach->getPassword());
    
    $entityManager->persist($coach);
    $entityManager->flush();
 return $this->redirectToRoute('liste_coaches');
}
return $this->render('admin/editCoach.html.twig', [
   
    'coach'=>$coach
]);
}


#[Route('/deleteCoach/{id}', name: 'delete_coach')]
  public function deleteCoach(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
  {
      $coach = $userRepository->find($id);
      if (!$coach) {
          throw $this->createNotFoundException(
              'No coach found for id '.$id
          );
      }
      $entityManager->remove($coach);
      $entityManager->flush();
      return $this->redirectToRoute('liste_coaches');
  }

  #[Route('/editMembre/{id}', name: 'edit_membre')]
public function editMembre(Request $request, int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
{
    $membre = $userRepository->find($id);
if (!$membre) { 
    throw $this->createNotFoundException(
        'No membre found for id '.$id
    );
}
if($request->isMethod('POST')){
    $email= $request->request->get('email');
    $nom= $request->request->get('name');
    $telephone= $request->request->get('telephone');
   
    $membre->setEmail($email);
    $membre->setNom($nom);
    $membre->setTelephone($telephone);
    $membre->setRoles(["MEMBRE"]);
    $membre->setPassword($membre->getPassword());
    
    $entityManager->persist($membre);
    $entityManager->flush();
 return $this->redirectToRoute('liste_membres');
}
return $this->render('admin/editMembre.html.twig', [
   
    'membre'=>$membre
]);
}



#[Route('/deleteMembre/{id}', name: 'delete_membre')]
  public function deleteMembre(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
  {
      $membre = $userRepository->find($id);
      if (!$membre) {
          throw $this->createNotFoundException(
              'No membre found for id '.$id
          );
      }
      $entityManager->remove($membre);
      $entityManager->flush();
      return $this->redirectToRoute('liste_membres');
  }

  


}

