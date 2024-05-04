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
use Twilio\Rest\Client;



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
         

          // Envoyer un message WhatsApp avec le nom du cours

          //na7iii hethi men commentaire!!!!!!!
        $this->envoyerMessageWhatsApp($user->getTelephone(), $cours->getNomCour());

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

//api whatsapp !!!!!
private function envoyerMessageWhatsApp($numeroTelephone, $nomCours)
{
    $sid    = "AC9c420f29e4603574c936348746f94949";
    $token  = "68f0ae53f83846f67e6c2ad62a4f756d";
    $twilio = new Client($sid, $token);

    $message = $twilio->messages
      ->create("whatsapp:+21624509366", // to
        array(
          "from" => "whatsapp:+14155238886",
          "body" => "Confirmation de participation au cours : $nomCours. Merci!"
        )
      );
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

#[Route('/chat', name: 'chat')]
public function chatbotHandler(Request $request, EntityManagerInterface $entityManager): Response
{
    // Logique de traitement du chatbot
    $message = $request->query->get('message');

    // Logique de traitement du chatbot
    $response = '';
    if (strpos($message, 'capacité') !== false) {
        // Récupérer la capacité du cours depuis la base de données
        $cours = $entityManager->getRepository(Cours::class)->findOneBy(['id' => 1]); // Remplacez '1' par l'ID du cours concerné
        if ($cours) {
            $response = 'La capacité du cours est de ' . $cours->getCapacite() . ' personnes.';
        } else {
            $response = 'Désolé, je n\'ai pas pu trouver d\'informations sur la capacité du cours.';
        }
    }

    // Ajouter une variable pour suivre si une question a déjà été posée
    $firstQuestion = true;

    // Répondre au chatbot avec la réponse générée
    return $this->render('GestionCours/chatbot.html.twig', [
        'response' => $response,
        'first_question' => $firstQuestion,
        'question' => $message, // Définir la variable question avec le message de la requête
    ]);
}

#[Route('/poser-question', name: 'poser_question')]
public function poserQuestion(Request $request, EntityManagerInterface $entityManager): Response
{
    // Récupérer la question soumise par le formulaire
    $question = $request->request->get('question');

    // Exemple de logique de traitement de la question (à adapter selon vos besoins)
    $response = '';

    if ($question) {
        // Utiliser une expression régulière pour détecter si la question concerne des conseils sportifs
        if (preg_match('/conseils?\s?sportifs?/i', $question)) {
            // Répondre avec des conseils sportifs
            $response = 'Voici quelques conseils sportifs : Assurez-vous de vous échauffer avant chaque séance d\'entraînement.
            Hydratez-vous régulièrement pendant l\'exercice.
            Écoutez votre corps et ne vous surmenez pas.';
        } else {
            // Logique de traitement de la question pour les cours par cible
            preg_match('/cours\s+(.*)\s*\?/i', $question, $matches);
            if (isset($matches[1])) {
                $cible = trim($matches[1]);
                // Recherche des cours dans la base de données par cible
                $cours = $entityManager->getRepository(Cours::class)->createQueryBuilder('c')
                    ->where('c.Cible LIKE :cible')
                    ->setParameter('cible', '%' . $cible . '%')
                    ->getQuery()
                    ->getResult();
                if ($cours) {
                    $coursNoms = array_map(function ($cour) {
                        return $cour->getNomCour();
                    }, $cours);
                    $response = 'Les cours ciblant ' . $cible . ' sont : ' . implode(', ', $coursNoms);
                } else {
                    $response = 'Désolé, je n\'ai pas trouvé de cours ciblant ' . $cible . '.';
                }
            } else {
                // Logique de traitement de la question pour les autres types de questions (capacité, catégorie, etc.)
                preg_match('/capacité.*\b(\w+)\b/i', $question, $matches);
                if (isset($matches[1])) {
                    $nomCours = trim($matches[1]);
                    // Recherche du cours dans la base de données par son nom
                    $cours = $entityManager->getRepository(Cours::class)->findOneBy(['nomCour' => $nomCours]);
                    if ($cours) {
                        $response = 'La capacité du cours ' . $cours->getNomCour() . ' est de ' . $cours->getCapacite() . ' personnes.';
                    } else {
                        $response = 'Désolé, je n\'ai pas pu trouver des informations sur la capacité du cours.';
                    }
                } else {
                    // Ajout de la logique pour répondre aux questions sur les cours d'une catégorie spécifique
                    preg_match('/cours\sde\scatégorie\s(.*)\?/i', $question, $matches);
                    // Vérifiez si un nom de catégorie a été trouvé
                    if (isset($matches[1])) {
                        $categorie = trim($matches[1]);
                        // Recherche des cours dans la base de données par catégorie
                        $cours = $entityManager->getRepository(Cours::class)->findBy(['Categorie' => $categorie]);
                        if ($cours) {
                            $coursNoms = array_map(function ($cour) {
                                return $cour->getNomCour();
                            }, $cours);
                            $response = 'Les cours de la catégorie ' . $categorie . ' sont : ' . implode(', ', $coursNoms);
                        } else {
                            $response = 'Désolé, je n\'ai pas trouvé de cours dans la catégorie ' . $categorie . '.';
                        }
                    } else {
                        $response = 'Désolé, je n\'ai pas pu extraire le nom du cours ou le mot "capacité" de votre question.';
                    }
                }
            }
        }
    } else {
        $response = 'Désolé, je n\'ai pas reçu de question.';
    }

    // Retourner une réponse HTML avec la réponse du chatbot
    return $this->render('GestionCours/chatbot.html.twig', [
        'response' => $response,
        'first_question' => true, // Vous pouvez définir 'first_question' à true si c'est la première question
        'question' => $question, // Définir la variable question avec le message de la requête
    ]);
}
//modif sghir 
//test e
}



