<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CoursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use App\Entity\Participation;
use App\Entity\Cours;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;




class CourMembreController extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


    #[Route('/cours', name: 'liste_cours')]
    public function listeCours(CoursRepository $coursRepository): Response
    {
        $cours = $coursRepository->findBy(['etat' => 1]); // Récupérer tous les cours avec un état égal à 1 depuis le repository
        // Convertir l'image BLOB en données binaires base64
        foreach ($cours as $cour) {
            // Convertir l'image BLOB en données binaires base64
            $cour->setImage(base64_encode(stream_get_contents($cour->getImage())));
        }

         // Filtrer les cours dont la capacité est supérieure à 0
         $cours = array_filter($cours, function($cour) {
            return $cour->getCapacite() > 0;
        });
    
        return $this->render('GestionCours/imageffect.html.twig', [
            'cours' => $cours,
        ]);
    }

    #[Route('/cours/{id}', name: 'voir_cours')]
public function voirCours(int $id, CoursRepository $coursRepository, Request $request): Response
{
    // Récupérer le cours depuis le référentiel en fonction de l'ID
    $cours = $coursRepository->find($id);

    // Vérifier si le cours existe
    if (!$cours) {
        throw new NotFoundHttpException('Cours non trouvé');
    }

    // Afficher les détails du cours dans un nouveau template
    return $this->render('GestionCours/voirPlus.html.twig', [
        'cours' => $cours,
    ]);
}

#[Route('/cours/{id}/participer', name: 'participer_cours')]
public function participerCours(int $id, CoursRepository $coursRepository, Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
{
    // Get the current user's email from the session
    $email =  $request->getSession()->get(Security::LAST_USERNAME);

    // Find the user entity based on the email
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

    // Find the course based on the ID
    $cours = $coursRepository->find($id);

    if ($cours->getCapacite() > 0) {
        // Decrement the course capacity by 1
        $cours->setCapacite($cours->getCapacite() - 1);

        // Create a new Participation entity
        $participation = new Participation();
        $participation->setNomCour($cours->getNomCour());
        $participation->setNomParticipant($user->getNom());
        $participation->setUser($user);

        // Persist the participation and update the course
        $entityManager->persist($participation);
        $entityManager->flush();

        // Send email confirmation
        $email = (new Email())
        ->from('FlexFlow <your_email@example.com>')
        ->to($email)
        ->subject('Confirmation de participation à un cours')
        ->html($this->renderView('GestionCours/email_confirmation.html.twig', [
            'user' => $user,
            'cours' => $cours,
        ]));

    $this->mailer->send($email);

        // Redirect to the confirmation page
        return $this->redirectToRoute('liste_cours');
    }    

}



        
}
