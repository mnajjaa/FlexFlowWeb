<?php

namespace App\Controller;


use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\prk;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BadWordFilter;
use App\Form\EtatFormType;
use Twilio\Rest\Client;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;



#[Route('/reclamation')]
class ReclamationController extends AbstractController
{

    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }
    



    #[Route('/listesReclamation', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator, ReclamationRepository $reclamationRepository): Response
    {

        

        $nonce = bin2hex(random_bytes(16));

        $queryBuilder = $reclamationRepository->createQueryBuilder('r');

        // Filter by state
        $etat = $request->query->get('etat');
        if (!empty($etat)) {
            $queryBuilder->andWhere('r.etat = :etat')
                         ->setParameter('etat', $etat);
        }

        // Search by date or title
        $searchDate = $request->query->get('date');
        $searchTitle = $request->query->get('titre');
        if (!empty($searchDate)) {
            $queryBuilder->andWhere('r.date_reclamation = :date')
                         ->setParameter('date', $searchDate);
        }
        if (!empty($searchTitle)) {
            $queryBuilder->andWhere('r.titre_reclamation LIKE :titre')
                         ->setParameter('titre', '%'.$searchTitle.'%');
        }

        // Get the query
        $query = $queryBuilder->getQuery();

        // Paginate the query
        $pagination = $paginator->paginate(
            $query, // Query to paginate
            $request->query->getInt('page', 1), // Current page number
            10 // Items per page
        );


      

        

        
    



        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
            'nonce' => $nonce,
            'pagination' => $pagination,
        ]);
    }



    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager ,BadWordFilter $badWordFilter,prk $bdf): Response
    {
        $user = new User();

        $reclamation = new Reclamation();

         // Définir la date de réclamation à la date système
         $reclamation->setDateReclamation(new \DateTime());

         // Définir l'état par défaut à "non traité"
         $reclamation->setEtat("Non_traite");
        
        
         // Get the current user's email from the session
         $email = $request->getSession()->get(Security::LAST_USERNAME);

        // Find the user entity based on the email
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);


        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $reclamation->setTitreReclamation($badWordFilter->filterText($reclamation->getTitreReclamation()));
            $reclamation->setDescription($badWordFilter->filterText($reclamation->getDescription()));


            $reclamation->setTitreReclamation($bdf->filterText($reclamation->getTitreReclamation()));
            $reclamation->setDescription($bdf->filterText($reclamation->getDescription()));


            $reclamation->setUser($user);

            $entityManager->persist($reclamation);        
            $entityManager->flush();

            $this->flashBag->add('success', 'La réclamation a été ajoutée avec succès.');


            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
        }
        

        return $this->renderForm('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    

    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(EtatFormType::class, $reclamation);
    $form->handleRequest($request);

    $accountSid = $_ENV['TWILIO_ACCOUNT_SID'];
    $authToken = $_ENV['TWILIO_AUTH_TOKEN'];
    $twilioPhoneNumber = $_ENV['TWILIO_NUMBER'];
    // Initialize Twilio client
    $twilio = new Client($accountSid, $authToken);

    if ($form->isSubmitted() && $form->isValid()) {
    
        // Enregistrer uniquement l'attribut etat


        $originalEtat = $entityManager->getUnitOfWork()->getOriginalEntityData($reclamation)['etat'] ?? 'Non_traite'; // Prend 'non traité' si non défini
        $newEtat = $reclamation->getEtat();

        $entityManager->persist($reclamation);
        $entityManager->flush();

        
/*
        if ($originalEtat !== 'Traite' && $newEtat === 'Traite') {
            // Envoie un SMS seulement si l'état change à "Traité"
            $message = $twilio->messages
                ->create(
                    "+21695523122", // Destination phone number from the form
                    [
                        'from' => $twilioPhoneNumber, // Your Twilio phone number
                        'body' => "Votre réclamation : {$reclamation->getTitreReclamation()}, a été traitée avec succès."
                    ]
                );
        }
*/

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('reclamation/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}

    

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }





}
