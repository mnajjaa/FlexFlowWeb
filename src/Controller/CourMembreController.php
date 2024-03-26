<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CoursRepository;
use App\Entity\Cours;

class CourMembreController extends AbstractController
{
    #[Route('/cours', name: 'liste_cours')]
    public function listeCours(CoursRepository $coursRepository): Response
    {
        $cours = $coursRepository->findAll(); // Récupérer tous les cours depuis le repository
        // Convertir l'image BLOB en données binaires base64
        foreach ($cours as $cour) {
            // Convertir l'image BLOB en données binaires base64
            $cour->setImage(base64_encode(stream_get_contents($cour->getImage())));
        }
 

        return $this->render('GestionCours/listeMembre.html.twig', [
            'cours' => $cours,
        ]);
    }



        
}
