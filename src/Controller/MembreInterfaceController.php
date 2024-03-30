<?php

namespace App\Controller;

use App\Entity\Offre;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\OffreRepository;

class MembreInterfaceController extends AbstractController
{
    #[Route('/membre/interface', name: 'app_membre_interface')]
    public function index(OffreRepository $offreRepository): Response
    {
        // Récupérer la liste des offres depuis le repository
        $offres = $offreRepository->findAll();

        // Passer les offres au template Twig pour les afficher
        return $this->render('membre_interface/index.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/offres', name: 'all_offers')]
    public function allOffers(OffreRepository $offreRepository): Response
    {
        // Récupérer la liste des offres depuis le repository
        $offres = $offreRepository->findAll();

        // Passer les offres au template Twig pour les afficher dans une liste
        return $this->render('membre_interface/all_offers.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/muscle-building', name: 'muscle_building')]
    public function muscleBuilding(OffreRepository $offreRepository): Response
    {
        $offres = $offreRepository->findBy(['specialite' => 'Musculation']);
        return $this->render('form_offre/consulter_offres2.html.twig', ['offres' => $offres]);
    }

    #[Route('/cardio', name: 'cardio')]
    public function cardio(OffreRepository $offreRepository): Response
    {
        $offres = $offreRepository->findBy(['specialite' => 'Cardio']);
        return $this->render('form_offre/consulter_offres2.html.twig', ['offres' => $offres]);
    }

    #[Route('/boxing', name: 'boxing')]
    public function boxing(OffreRepository $offreRepository): Response 
    {
        $offres = $offreRepository->findBy(['specialite' => 'Boxe']);
        return $this->render('form_offre/consulter_offres2.html.twig', ['offres' => $offres]);
    }

    #[Route('/yoga', name: 'yoga')]
    public function yoga(OffreRepository $offreRepository): Response
    {
        $offres = $offreRepository->findBy(['specialite' => 'Yoga']);
        return $this->render('form_offre/consulter_offres2.html.twig', ['offres' => $offres]);
    }
}
