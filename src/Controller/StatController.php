<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DemandeRepository;

#[Route('/statistiques')]
class StatistiqueController extends AbstractController
{
    #[Route('/offres-demandees', name: 'statistiques_offres_demandees')]
    public function offresDemandees(DemandeRepository $demandeRepository): Response
    {
        $statistiques = $demandeRepository->countByOffre();

        // Vous pouvez maintenant passer les statistiques à votre vue pour affichage
        return $this->render('stat/offres_demandees.html.twig', [
            'statistiques' => $statistiques,
        ]);
    }
}
