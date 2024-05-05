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
use App\Form\ChangePasswordFormType;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Service\TwoFactorAuthenticator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\IpInfoService;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\BarChart;





class AdminController extends AbstractController
{
    private $twoFactorAuthenticator;
    private $IpInfoService;

    public function __construct(TwoFactorAuthenticator $twoFactorAuthenticator,IpInfoService $IpInfoService)
    {
        $this->twoFactorAuthenticator = $twoFactorAuthenticator;
        $this->IpInfoService = $IpInfoService;


    }
    static $mdp=false;
    // static $message="";
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(UserRepository $userRepository): Response
    {
        $recentylyadd=0;
        $locations=[];
        $users=$userRepository->findAll();
        foreach($users as $user){
            if($user->getCreatedAt() > new \DateTime("-7 days")&&$user->getRoles()==["MEMBRE"])
            {
               $recentylyadd++;
            }
            }
        foreach($users as $user){
            $ipinfoservice=$user->getLoginHistories();
            foreach($ipinfoservice as $ipinfo){
                $ip=$ipinfo->getIpAdress();
                $location=$this->IpInfoService->getIpInfo($ip);
                dump($location);
                array_push($locations,$location);
            }
        }
       
      $jour_de_semaine = "";
        $nombreMembres=0;
        $nombreCoaches=0;
        $map_data = array(
            "Monday" => array("members" => 0, "coaches" => 0),
            "Tuesday" => array("members" => 0, "coaches" => 0),
            "Wednesday" => array("members" => 0, "coaches" => 0),
            "Thursday" => array("members" => 0, "coaches" => 0),
            "Friday" => array("members" => 0, "coaches" => 0),
            "Saturday" => array("members" => 0, "coaches" => 0),
            "Sunday" => array("members" => 0, "coaches" => 0)
        );
        $barChart = new BarChart();
        //$users=$userRepository->RecupérerUsers();
        $barChart->getOptions()->setTitle('Membres et Coaches par jour de la semaine');
        $barChart->getOptions()->getHAxis()->setTitle('Nombre de personnes');
        $barChart->getOptions()->getHAxis()->setMinValue(0);
        $barChart->getOptions()->getVAxis()->setTitle('Day of the week');
        $barChart->getOptions()->setWidth(900);
        $barChart->getOptions()->setHeight(500);
       

$membres=$userRepository->findByRole("MEMBRE");
$coaches=$userRepository->findByRole("COACH");

        foreach($membres as $membre){
           foreach($membre->getLoginHistories() as $loginHistory){
                  if($loginHistory->getLoginDate()>new \DateTime(" -7 days ")&& $loginHistory->getLoginDate())
                {
                    
                    
                    $date = $loginHistory->getLoginDate(); 
                    $jour_de_semaine = date('l', strtotime($date->format('Y-m-d')));
                    $map_data[$jour_de_semaine]["members"]++;
                   
  
                
            }
        }
    }
    
foreach($coaches as $coach){
    foreach($coach->getLoginHistories() as $loginHistory){
        if($loginHistory->getLoginDate() > new \DateTime("-7 days")){
            $jour_de_semaine = date('l', strtotime($date->format('Y-m-d')));
            $map_data[$jour_de_semaine]["coaches"]++;
        }
    }
}

        $barChart->getData()->setArrayToDataTable([
            ['Day of the week', 'Membres', 'Coaches'],
            ["Sunday",  $map_data["Sunday"]["members"], $map_data["Sunday"]["coaches"]],
             ['Monday', $map_data["Monday"]["members"] , $map_data["Monday"]["coaches"]],
            ['Tuesday',$map_data["Tuesday"]["members"]  ,  $map_data["Tuesday"]["coaches"]     ],
            ['Wednesday', $map_data["Wednesday"]["members"], $map_data["Wednesday"]["coaches"]],
            ['Thursday', $map_data["Thursday"]["members"], $map_data["Thursday"]["coaches"]],
            ['Friday', $map_data["Friday"]["members"], $map_data["Friday"]["coaches"]],
            ['Saturday', $map_data["Saturday"]["members"], $map_data["Saturday"]["coaches"]]
             
            
        ]);
       
        
        $totalUsers = count($userRepository->findAll());
        $nombreMembres=count($membres);
        $nombreCoaches=count($coaches);
        

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'barChart' => $barChart,
            'nombreMembres'=>$nombreMembres,
            'nombreCoaches'=>$nombreCoaches,
            'totalUsers'=>$totalUsers,
            'locations'=>$locations,
            'recentylyadd'=>$recentylyadd,

    
        ]);
    }

    
    #[Route('/profileAdmin', name: 'admin_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $locations=[];
        $user = new User();
        
        
        $email =  $request->getSession()->get(Security::LAST_USERNAME);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        $currentUserId = $user->getId(); // Get the id of the current user
        
        $ipinfoservice = $user->getLoginHistories();
        foreach($ipinfoservice as $ipinfo){
            $ip = $ipinfo->getIpAdress();
            $loginDate = $ipinfo->getLoginDate(); // Get the login date
            $navigateur = $ipinfo->getNavigateur(); // Get the navigateur information

        
            // Check if the user id in the LoginHistory object matches the current user id
            if ($ipinfo->getUser()->getId() == $currentUserId) {
                $location = $this->IpInfoService->getIpInfo($ip);
                dump($location);
                array_push($locations, array('location' => $location, 'loginDate' => $loginDate, 'navigateur' => $navigateur));
            }
        }
        
        // $id = $user->getId();
        // Remove the unused variable $id
        
        return $this->render('admin/profile.html.twig', [
            'admin' => $user,
            'locations'=>$locations

        ]);
    }
#[Route('/editPwdAdmin', name: 'admin_edit_pwd')]
public function editPwd(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, UserPasswordHasherInterface $passwordHasher): Response
{
    $erreur=false;
    
    $email = $request->getSession()->get(Security::LAST_USERNAME);
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    
    
    if($request->isMethod('POST')) {
        
        if ( $request->get('actualPassword') && !$passwordHasher->isPasswordValid($user, $request->get('actualPassword'))) {
            // $this->addFlash('reset_password_error', 'Old password is incorrect');
            return $this->redirectToRoute('admin_edit_profile', ['erreur'=>true]);
           
        }
        else {
      
            self::$mdp=true;
            // return $this->render('admin/editProfileAdmin.html.twig', [
            //     'mdp'=>self::$mdp,
            //     'admin' => $user
            // ]);
        }
        if($request->get('plainPassword')  && $request->get('plainPasswordConfirm')){
          
        
        if($request->get('plainPassword') != $request->get('plainPasswordConfirm')){
           
            return $this->redirectToRoute('admin_edit_profile');
        }
        else{
          
        // var_dump($request->get('plainPassword'));
        $encodedPassword = $passwordHasher->hashPassword(
            $user,
            $request->get('plainPassword')
        );

        $user->setPassword($encodedPassword);
        $entityManager->persist($user);
            $entityManager->flush();
            // var_dump($user);
            return $this->redirectToRoute('admin_edit_profile');
    }
    }
    return $this->render('admin/editProfileAdmin.html.twig', [
        'mdp'=>self::$mdp,
        'admin' => $user,
        'erreur'=>$erreur
    ]);

}
}


#[Route('/editProfileAdmin', name: 'admin_edit_profile')]
public function editProfile(Request $request, EntityManagerInterface $entityManager, SessionInterface $session,UserPasswordHasherInterface $passwordHasher,bool $erreur=false): Response
{
    


    $erreur=false;
    $mdp=false;
    $user = new User();
    
    $email = $request->getSession()->get(Security::LAST_USERNAME);
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    $secret=$this->twoFactorAuthenticator->generateSecret();
    $qrCode=$this->twoFactorAuthenticator->getQrCode($secret, $user->getEmail(), 'FlexFlow');
    
    if (!$user) {
        throw $this->createNotFoundException('User not found.');
    }

    
if ($request->isMethod('POST')) {

    //hatitha f cmnt khater edit profile maadch theb temchy !!!!!
    
    // if(  !$passwordHasher->isPasswordValid($user, $request->get('actualPassword'))){
        
      
    //     $erreur=true;
    //     return $this->render('admin/editProfileAdmin.html.twig', [
    //         'admin' => $user,
    //         'mdp'=>$mdp,
    //         'erreur'=>$erreur
    //             ]);
    //     // $this->addFlash('reset_password_error', 'Old password is incorrect');
    //     // return $this->redirectToRoute('admin_edit_profile');
    // }
    // else {
    //     $mdp=true;
    //     // $mdp=true;
    //     return $this->render('admin/editProfileAdmin.html.twig', [
    //         'mdp'=>$mdp,
    //         'admin' => $user,
    //         'erreur'=>$erreur
    //     ]);
    // }

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
        //return $this->redirectToRoute('admin_profile');
        
    }        
        return $this->render('admin/editProfileAdmin.html.twig', [
        'admin' => $user,
        'mdp'=>$mdp,
        'erreur'=>$erreur,
        'qrCode'=>$qrCode,
        'secret'=>$secret
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
        
        return $this->render('admin/addCoach.html.twig',[
    'message'=>""]);
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
        $coach->setImage("7c62c977256064c61946037d427a0e0c.png");
        $coach->setNom($nom);
        $coach->setCreatedAt(new \DateTime());
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
        
       // return new JsonResponse(['message' => "User not found"], Response::HTTP_OK);
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
  #[Route('/checkUser', name: 'check_user')]
  public function checkUser(Request $request, UserRepository $userRepository): Response

  {

    
      $email = $request->request->get('username');
      $user = $userRepository->findOneBy(['email' => $email]);
    //  if($email==null){
    //     echo 'Email is empty';
    //  }
    //  else{
    //     echo 'Email is not empty';
       
    //  }
//       var_dump($user);
      if ($user) {
      
       return   new Response("true");
      }
       return new Response("false");
  }

  
  #[Route('/active2fa', name: 'activte_2fa')]
  public function active2fa(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
  {
    if($request->isMethod('POST')){
        $secret = $request->request->get('secretKey');
        $code = $request->request->get('otp');
        if (!$this->twoFactorAuthenticator->validateOTPCode($secret, $code)) {
            return $this->redirectToRoute('admin_edit_profile');
        }
      $email = $request->getSession()->get(Security::LAST_USERNAME);
      $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
   
      $user->setMfaSecret($secret);
      $user->setMfaEnabled(true);
      $entityManager->persist($user);
      $entityManager->flush();
      return $this->redirectToRoute('admin_edit_profile');
  }
    return $this->redirectToRoute('admin_edit_profile');
    }

}