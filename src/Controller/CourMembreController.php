<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Cours;
use App\Entity\Rating;
use App\Entity\Participation;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Mime\Email;
use App\Repository\CoursRepository;
use App\Repository\RatingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\Notify\NotifierInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



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
public function voirCours(int $id, CoursRepository $coursRepository, Request $request, EntityManagerInterface $entityManager, RatingRepository $ratingRepository): Response
{
    // Récupérer le cours depuis le référentiel en fonction de l'ID
    //$cours = $coursRepository->find($id);
    $cours = $this->getDoctrine()->getRepository(cours::class)->find($id);

    // Vérifier si le cours existe
    if (!$cours) {
        throw new NotFoundHttpException('Cours non trouvé');
    }

     // Obtenir les totaux de likes et dislikes
     $totalLikes = $ratingRepository->getTotalLikes($cours->getNomCour());
     $totalDislikes = $ratingRepository->getTotalDislikes($cours->getNomCour());

    // Vérifier si l'utilisateur a déjà participé à ce cours
    $email = $request->getSession()->get(Security::LAST_USERNAME);
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    $existingParticipation = $entityManager->getRepository(Participation::class)->findOneBy([
        'user' => $user,
        'nomCour' => $cours->getNomCour()
    ]);

     $cours->image = base64_encode(stream_get_contents($cours->getImage()));
    // Ajouter une variable pour indiquer si le membre a déjà participé
    $dejaParticipe = ($existingParticipation !== null);

    // Vérifier si l'utilisateur a déjà évalué ce cours
    $alreadyRated = $ratingRepository->findOneBy([
        'user' => $user,
        'nom_cour' => $cours->getNomCour()
    ]);

    // Récupérer la catégorie du cours visité
    $categorie = $cours->getCategorie();

    // Récupérer deux autres cours de la même catégorie que le cours visité
    $autresCours = $coursRepository->findRandomCoursByCategory($categorie, 2, $id);

    // Transformez les images en base64 pour les afficher dans le template Twig
    foreach ($autresCours as $cour) {
        $cour->setImage(base64_encode(stream_get_contents($cour->getImage())));
    }

   // $cours->image = base64_encode(stream_get_contents($cours->getImage()));
    // Afficher les détails du cours dans un nouveau template
    return $this->render('GestionCours/voirPlus.html.twig', [
        'cours' => $cours,
        'dejaParticipe' => $dejaParticipe,
        'autresCours' => $autresCours,
        
        'totalLikes' => $totalLikes,
        'totalDislikes' => $totalDislikes,
        'alreadyRated' => ($alreadyRated !== null),
    ]);
}

#[Route('/cours/{id}/participer', name: 'participer_cours')]
public function participerCours(int $id, CoursRepository $coursRepository, Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
{
    // Get the current user's email from the session
    $email = $request->getSession()->get(Security::LAST_USERNAME);

    // Find the user entity based on the email
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

    // Find the course based on the ID
    $cours = $coursRepository->find($id);

    // Check if the user has already participated in this course
    $existingParticipation = $entityManager->getRepository(Participation::class)->findOneBy([
        'user' => $user,
        'nomCour' => $cours->getNomCour()
    ]);

     // Ajoutez une variable pour indiquer si le membre a déjà participé
     $dejaParticipe = ($existingParticipation !== null);

    // If the user has already participated, redirect with an error message
    if ($existingParticipation) {
        $this->addFlash('error', 'Vous avez déjà participé à ce cours.');
        return $this->redirectToRoute('liste_cours');
    }

    // If the course still has capacity
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

         // Ajout d'un message flash de succès
         $this->addFlash('success', 'Participation succès.');
         

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

////commentaire aprés merge////

#[Route('/cours/{id}/evaluer', name: 'evaluer_cours')]
public function evaluerCours(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    // Récupérer l'utilisateur connecté
    $user = $this->getUser();
    
    // Récupérer les données d'évaluation depuis la requête AJAX
    $data = json_decode($request->getContent(), true);

    // Récupérer le cours à partir de son ID
    $cours = $entityManager->getRepository(Cours::class)->find($id);

    // Vérifier si le cours existe
    if (!$cours) {
        return new JsonResponse(['error' => 'Cours non trouvé'], 404);
    }

    // Vérifier si l'utilisateur est connecté
    if (!$user) {
        return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
    }

    // Récupérer la valeur de notation
    $ratingValue = ($data['rating'] === 'like') ? 1 : -1;
    $liked = ($ratingValue === 1);
    $disliked = ($ratingValue === -1);

    // Créer une nouvelle instance de l'entité Rating
    $rating = new Rating();
    $rating->setUser($user);
    $rating->setNomCour($cours->getNomCour());
    $rating->setRating($ratingValue);
    $rating->setLiked($liked);
    $rating->setDisliked($disliked);

    // Persist et flush l'entité Rating
    $entityManager->persist($rating);
    $entityManager->flush();

    // Réponse JSON pour indiquer que l'évaluation a été enregistrée avec succès
    return new JsonResponse(['success' => true]);
}



#[Route('/cours/{id}/likes', name: 'get_likes_dislikes')]
public function getLikesDislikes(int $id, EntityManagerInterface $entityManager): JsonResponse
{
    // Récupérer le cours à partir de son ID
    $cours = $entityManager->getRepository(Cours::class)->find($id);

    // Vérifier si le cours existe
    if (!$cours) {
        return new JsonResponse(['error' => 'Cours non trouvé'], 404);
    }

    // Récupérer le nombre de likes et de dislikes pour le cours
    $likesCount = $entityManager->getRepository(Rating::class)->count(['nomCour' => $cours->getNomCour(), 'liked' => true]);
    $dislikesCount = $entityManager->getRepository(Rating::class)->count(['nomCour' => $cours->getNomCour(), 'disliked' => true]);

    // Retourner le nombre de likes et de dislikes sous forme de réponse JSON
    return new JsonResponse(['likes' => $likesCount, 'dislikes' => $dislikesCount]);
}




#[Route('/cours/{id}/annuler-participation', name: 'annuler_participation_cours')]
public function annulerParticipationCours(int $id, EntityManagerInterface $entityManager): Response
{
    // Récupérer l'utilisateur connecté
    $user = $this->getUser();

    // Récupérer le cours
    $cours = $entityManager->getRepository(Cours::class)->find($id);

    // Vérifier si l'utilisateur a déjà participé à ce cours
    $participation = $entityManager->getRepository(Participation::class)->findOneBy([
        'user' => $user,
        'nomCour' => $cours->getNomCour()
    ]);

    // Si l'utilisateur a participé, annuler sa participation
    if ($participation) {
        // Augmenter la capacité du cours
        $cours->setCapacite($cours->getCapacite() + 1);

        // Supprimer l'entrée de participation
        $entityManager->remove($participation);
        $entityManager->flush();

        // Rediriger avec un message de succès
        $this->addFlash('success', 'Participation annulée avec succès.');
    }

    // Rediriger vers la liste des cours
    return $this->redirectToRoute('voir_cours', ['id' => $id]);
}

        
}