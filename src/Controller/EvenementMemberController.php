<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EvenementMemberController extends AbstractController
{
    #[Route('/evenement/member', name: 'app_evenement_member')]
    public function index(): Response
    {
        return $this->render('evenement_member/index.html.twig', [
            'controller_name' => 'EvenementMemberController',
        ]);
    }


    #[Route('/evenements/{id}', name: 'voir_evenements')]
    public function voirCours(int $id, EvenementRepository $EvenementRepository, Request $request): Response
    {
        // Récupérer le cours depuis le référentiel en fonction de l'ID
        //$cours = $coursRepository->find($id);
        $evenements = $this->getDoctrine()->getRepository(Evenement::class)->find($id);
    
        // Vérifier si le cours existe
        if (!$evenements) {
            throw new NotFoundHttpException('evenement non trouvé');
        }
    
        $evenements->image = base64_encode(stream_get_contents($evenements->getImage()));
        // Afficher les détails du cours dans un nouveau template
        return $this->render('evenement_member/voir-plus.html.twig', [
            'evenements' => $evenements,
           
        ]);
    }
    
    
    #[Route('/evenements', name: 'liste_evenement')]
public function listeEvenement(Request $request, EvenementRepository $EvenementRepository): Response
{
   
    $evenements = $EvenementRepository->findBy(['etat' => 1]);
    foreach ($evenements as $evenement) {
        $evenement->setImage(base64_encode(stream_get_contents($evenement->getImage())));
    }

    return $this->render('evenement_member/voir.html.twig', [
        'evenements' => $evenements,
    ]);
}


   

}
