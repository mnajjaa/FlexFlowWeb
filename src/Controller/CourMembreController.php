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




class CourMembreController extends AbstractController
{
    #[Route('/cours', name: 'liste_cours')]
    public function listeCours(CoursRepository $coursRepository): Response
    {
        $cours = $coursRepository->findBy(['etat' => 1]); // Récupérer tous les cours avec un état égal à 1 depuis le repository
        // Convertir l'image BLOB en données binaires base64
        foreach ($cours as $cour) {
            // Convertir l'image BLOB en données binaires base64
            $cour->setImage(base64_encode(stream_get_contents($cour->getImage())));
        }
    
        return $this->render('GestionCours/listeMembre.html.twig', [
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
    return $this->render('GestionCours/voirCours.html.twig', [
        'cours' => $cours,
    ]);
}

#[Route('/cours/{id}/participer', name: 'participer_cours')]
public function participerCours(int $id, CoursRepository $coursRepository, Request $request, EntityManagerInterface $entityManager): Response
{
    // Get the current user's email from the session
    $email =  $request->getSession()->get(Security::LAST_USERNAME);

    // Find the user entity based on the email
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

    // Find the course based on the ID
    $cours = $coursRepository->find($id);

    $participation = new Participation();
    $participation->setNomCour($cours->getNomCour());
    $participation->setNomParticipant($user->getNom());
    $participation->setUser($user);
        // Persist the participation
    $entityManager->persist($participation);
    $entityManager->flush();
    // Redirect to the confirmation page
    return $this->redirectToRoute('liste_cours');    

}

        
}
