<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReservationRepository;
use App\Repository\FavorisRepository;
class StatisticsController extends AbstractController
{
    #[Route('/statistics', name: 'app_statistics')]
    public function index(ReservationRepository $reservationRepo, FavorisRepository $favorisRepo): Response
    {
        $mostReservedEvent = $reservationRepo->findMostReservedEvent();
        $mostLovedEvent = $favorisRepo->findMostLovedEvent();
        $mostHatedEvent = $favorisRepo->findMostUnlovedEvent();
    
        // Création du tableau de données
        $data = [
            'mostReservedName' => $mostReservedEvent ? $mostReservedEvent['eventName'] : 'No event',
            'mostLovedName' => $mostLovedEvent ? $mostLovedEvent['eventName'] : 'No event',
            'mostHatedName' => $mostHatedEvent ? $mostHatedEvent['eventName'] : 'No event',
            'mostReservedCount' => $mostReservedEvent ? $mostReservedEvent['reservationCount'] : 0,
            'mostLovedCount' => $mostLovedEvent ? $mostLovedEvent['loveCount'] : 0,
            'mostHatedCount' => $mostHatedEvent ? $mostHatedEvent['unloveCount'] : 0,
        ];
    
        // Rendu de la vue avec les données
        return $this->render('statistics/index.html.twig', [
            'data' => $data,
        ]);
    }
    

}
