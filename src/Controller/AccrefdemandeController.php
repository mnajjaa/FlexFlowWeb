<?php

namespace App\Controller;
use App\Entity\Demande;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class AccrefdemandeController extends AbstractController
{
    #[Route('/accrefdemande', name: 'app_accrefdemande')]

    public function index(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $demande = $entityManager->getRepository(Demande::class)->findAll();

        return $this->render('accrefdemande/index.html.twig', [
            'demande' => $demande,
        ]);
    }

    #[Route('accepterdemande/{id}', name: 'accepter_demande')]
    public function accrefDemande($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $demande = $entityManager->getRepository(Demande::class)->find($id);
    
        if (!$demande) {
            throw $this->createNotFoundException('Aucune demande trouvée pour cet identifiant : ' . $id);
        }
    
        $demande->setEtat('acceptée'); // Assuming 'Etat' is the property for the state of the demand
        $entityManager->flush();
    
        return $this->redirectToRoute('app_accrefdemande');
    }
    
    #[Route('/refuserdemande/{id}', name: 'refuser_demande')]
    public function refuserDemande($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $demande = $entityManager->getRepository(Demande::class)->find($id);
    
        if (!$demande) {
            throw $this->createNotFoundException('Aucune demande trouvée pour cet identifiant : ' . $id);
        }
    
        $demande->setEtat('refusée'); // Assuming 'Etat' is the property for the state of the demand
        $entityManager->flush();
    
        return $this->redirectToRoute('app_accrefdemande');
    }
}    