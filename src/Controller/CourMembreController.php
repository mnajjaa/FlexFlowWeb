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
use Knp\Component\Pager\PaginatorInterface;



class CourMembreController extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


    #[Route('/cours', name: 'liste_cours')]
public function listeCours(Request $request, CoursRepository $coursRepository, PaginatorInterface $paginator): Response
{
    $categories = $coursRepository->findDistinctCategories();
    $objectifs = $coursRepository->findDistinctObjectifs();
    $cibles = $coursRepository->findDistinctCibles();
    $selectedCategory = $request->query->get('categorie');
    $selectedObjectif = $request->query->get('objectif');
    $selectedCible = $request->query->get('cible');
    $cours = $coursRepository->findBy(['etat' => 1]);

    if ($selectedCategory) {
        $cours = array_filter($cours, function ($cour) use ($selectedCategory) {
            return $cour->getCategorie() === $selectedCategory;
        });
    }

    if ($selectedObjectif) {
        $cours = array_filter($cours, function ($cour) use ($selectedObjectif) {
            return $cour->getObjectif() === $selectedObjectif;
        });
    }

    if ($selectedCible) {
        $cours = array_filter($cours, function ($cour) use ($selectedCible) {
            return $cour->getCible() === $selectedCible;
        });
    }

    foreach ($cours as $cour) {
        $cour->setImage(base64_encode(stream_get_contents($cour->getImage())));
    }

    $cours = array_filter($cours, function ($cour) {
        return $cour->getCapacite() > 0;
    });

     // Pagination
     $pagination = $paginator->paginate(
        $cours, // Requête à paginer
        $request->query->getInt('page', 1), // Numéro de page par défaut
        6 // Nombre d'éléments par page
    );

    return $this->render('GestionCours/imageffect.html.twig', [
        'pagination' => $pagination,
        'categories' => $categories,
        'objectifs' => $objectifs,
        'cibles' => $cibles,
        'selectedCategory' => $selectedCategory,
        'selectedObjectif' => $selectedObjectif,
        'selectedCible' => $selectedCible,
    ]);
}


    #[Route('/cours/{id}', name: 'voir_cours')]
public function voirCours(int $id, CoursRepository $coursRepository, Request $request): Response
{
    // Récupérer le cours depuis le référentiel en fonction de l'ID
    //$cours = $coursRepository->find($id);
    $cours = $this->getDoctrine()->getRepository(cours::class)->find($id);

    // Vérifier si le cours existe
    if (!$cours) {
        throw new NotFoundHttpException('Cours non trouvé');
    }

    $cours->image = base64_encode(stream_get_contents($cours->getImage()));
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
